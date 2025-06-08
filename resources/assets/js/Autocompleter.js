export class Autocompleter {
  constructor (Sk) {
    this.Sk = Sk
    this.imports = []
    this.vars = {}
    this.builtinVars = {}
    this.functions = {}
    this.classes = {}
  }

  setCode (code) {
    this.code = code
  }

  getPrototypeDetails (val) {
    if (typeof val !== 'function' || !val.prototype) {
      return { methods: [], properties: [] }
    }

    const methods = []
    const properties = []

    for (const [key, value] of Object.entries(val.prototype)) {
      if (
        !key.startsWith('__') &&
        !key.startsWith('$') &&
        !key.includes('$') &&
        key !== 'toFixed'
      ) {
        if (typeof value === 'function' || (value && typeof value.tp$call === 'function')) {
          methods.push(key)
        } else {
          properties.push(key)
        }
      }
    }
    return { methods, properties }
  }

  getFunctionArgsFromTextSig (func) {
    const textsig = func?.$textsig
    if (!textsig) return []

    // Remove parentheses and split
    return textsig
      .replace(/^\(.*?,\s*/, '(') // remove $module if present
      .replace(/[()]/g, '') // remove parens
      .split(/[,/]/) // split on commas and slash
      .map(s => s.trim())
      .filter(name => name && name !== '$module')
  }

  getConstructorArgsFromClassDef (classDef) {
    if (!classDef || !(classDef instanceof this.Sk.astnodes.ClassDef)) {
      return '()'
    }

    for (const node of classDef.body) {
      if (
        node instanceof this.Sk.astnodes.FunctionDef &&
        node.name?.v === '__init__'
      ) {
        const args = node.args.args.map(arg => arg.arg.v)
        // Remove 'self' if it's the first argument
        const filteredArgs = args[0] === 'self' ? args.slice(1) : args
        return `(${filteredArgs.join(', ')})`
      }
    }

    return '()'
  }

  inferForLoopVarType (forNode) {
    const iter = forNode.iter

    if (!iter) return 'unknown'

    // Case: range(n)
    if (iter instanceof this.Sk.astnodes.Call &&
        iter.func instanceof this.Sk.astnodes.Name &&
        iter.func.id.v === 'range') {
      return 'int'
    }

    // Case: list literal
    if (iter instanceof this.Sk.astnodes.List) {
      const el = iter.elts[0]
      if (el instanceof this.Sk.astnodes.Str) return 'str'
      if (el instanceof this.Sk.astnodes.Num) {
        return Number.isInteger(el.n.v) ? 'int' : 'float'
      }
      return 'list element'
    }

    // Case: string literal
    if (iter instanceof this.Sk.astnodes.Str) return 'str'

    // Case: tuple literal
    if (iter instanceof this.Sk.astnodes.Tuple) return 'tuple element'

    // Case: Name — could be anything, maybe look it up later
    if (iter instanceof this.Sk.astnodes.Name) {
      const name = iter.id.v
      const varInfo = this.vars[name]
      if (varInfo && varInfo.datatype) {
        return varInfo.datatype
      }
      return name + ' element'
    }

    // Case: function call we don't recognise
    if (iter instanceof this.Sk.astnodes.Call) return iter.func.id.v + ' element'

    return 'unknown'
  }

  processNodes (nodes, level, lineNo) {
    for (let i = 0; i < nodes.length; i++) {
      if (nodes[i].lineno > lineNo) {
        break
      }

      if (
        nodes[i] instanceof this.Sk.astnodes.While ||
        nodes[i] instanceof this.Sk.astnodes.Forever
      ) {
        this.processNodes(nodes[i].body, level, lineNo)
      } else if (nodes[i] instanceof this.Sk.astnodes.If) {
        this.processNodes(nodes[i].body, level, lineNo)
        this.processNodes(nodes[i].orelse, level, lineNo)
      } else if (nodes[i] instanceof this.Sk.astnodes.For) {
        const datatype = this.inferForLoopVarType(nodes[i])
        let lookup = 'unknown'
        if (datatype === 'int') {
          lookup = 'int_$rw$'
        } else if (datatype === 'float') {
          lookup = 'float_$rw$'
        } else if (datatype === 'str') {
          lookup = 'str'
        }
        const details = this.getPrototypeDetails(this.Sk.builtins[lookup])
        this.vars[nodes[i].target.id.v] = {
          type: 'Variable',
          datatype,
          name: nodes[i].target.id.v,
          methods: details.methods,
          properties: details.properties
        }
        this.processNodes(nodes[i].body, level, lineNo)
        this.processNodes(nodes[i].orelse, level, lineNo)
      } else if (nodes[i] instanceof this.Sk.astnodes.ImportFrom) {
        if (!this.imports.includes(nodes[i].module.v)) {
          let found = false
          if ('./' + nodes[i].module.v + '.py' in this.Sk.builtinFiles.files) {
            this.setCode(this.Sk.builtinFiles.files['./' + nodes[i].module.v + '.py'])
            found = true
          } else if ('src/lib/' + nodes[i].module.v + '.py' in this.Sk.builtinFiles.files) {
            this.setCode(this.Sk.builtinFiles.files['src/lib/' + nodes[i].module.v + '.py'])
            found = true
          }
          if (found) {
            this.imports.push(nodes[i].module.v)

            this.getCompletions(level + 1)
          } else {
            console.log('Did not parse module: ' + nodes[i].module.v)
          }
        }
      } else if (nodes[i] instanceof this.Sk.astnodes.Assign) {
        let newVar
        if (nodes[i].targets[0] instanceof this.Sk.astnodes.Name) {
          if (nodes[i].value instanceof this.Sk.astnodes.Dict) {
            const keys = []
            for (const key of nodes[i].value.keys) {
              if (key instanceof this.Sk.astnodes.Str) {
                keys.push('\'' + key.s.v + '\'')
              } else if (key instanceof this.Sk.astnodes.Num) {
                keys.push(key.n.v)
              } else if (key instanceof this.Sk.astnodes.NameConstant) {
                keys.push(key.value.v)
              }
            }
            const details = this.getPrototypeDetails(this.Sk.builtins.dict)
            newVar = {
              type: 'dict',
              datatype: 'dict',
              name: nodes[i].targets[0].id.v,
              keys,
              methods: details.methods,
              properties: details.properties
            }
          } else if (nodes[i].value instanceof this.Sk.astnodes.Call) {
            const calledName = nodes[i].value.func.id.v
            if (calledName === 'dict') {
              const keys = []
              if (nodes[i].value.args.length > 0) {
                if (nodes[i].value.args[0] instanceof this.Sk.astnodes.Dict) {
                  for (const key of nodes[i].value.args[0].keys) {
                    if (key instanceof this.Sk.astnodes.Str) {
                      keys.push('\'' + key.s.v + '\'')
                    } else if (key instanceof this.Sk.astnodes.Num) {
                      keys.push(key.n.v)
                    } else if (key instanceof this.Sk.astnodes.NameConstant) {
                      keys.push(key.value.v)
                    }
                  }
                }
              } else if (nodes[i].value.keywords.length > 0) {
                for (const keyword of nodes[i].value.keywords) {
                  keys.push('\'' + keyword.arg.v + '\'')
                }
              }
              const details = this.getPrototypeDetails(this.Sk.builtins.dict)
              newVar = {
                type: 'dict',
                datatype: 'dict',
                name: nodes[i].targets[0].id.v,
                keys,
                methods: details.methods,
                properties: details.properties
              }
            // } else if (["set", "list", "dict", "tuple", "str", "int", "float"].includes(calledName)) {
            } else if (calledName in this.classes) {
              newVar = {
                type: nodes[i].value.func.id.v,
                datatype: calledName,
                methods: [...this.classes[nodes[i].value.func.id.v].methods],
                properties: [...this.classes[nodes[i].value.func.id.v].properties]
              }
            } else {
              newVar = {
                type: 'Variable',
                datatype: 'Unknown',
                name: nodes[i].targets[0].id.v
              }
            }
          } else {
            let datatype
            let lookup
            if (nodes[i].value instanceof this.Sk.astnodes.Num) {
              datatype = 'float'
              lookup = 'float_$rw$'
              if (Number.isInteger(nodes[i].value.n.v)) {
                datatype = 'int'
                lookup = 'int_$rw$'
              }
            } else if (nodes[i].value instanceof this.Sk.astnodes.Str) {
              datatype = 'str'
              lookup = 'str'
            } else if (nodes[i].value instanceof this.Sk.astnodes.List) {
              datatype = 'list'
              lookup = 'list'
            } else if (nodes[i].value instanceof this.Sk.astnodes.Tuple) {
              datatype = 'tuple'
              lookup = 'tuple'
            } else if (nodes[i].value instanceof this.Sk.astnodes.Set) {
              datatype = 'set'
              lookup = 'set'
            } else if (nodes[i].value instanceof this.Sk.astnodes.NameConstant) {
              if (nodes[i].value.value.v === 1 || nodes[i].value.value.v === 0) {
                datatype = 'bool'
                lookup = 'bool'
              } else {
                datatype = 'Constant'
                lookup = ''
              }
            } else {
              datatype = 'Unknown'
              lookup = ''
            }
            const details = this.getPrototypeDetails(this.Sk.builtins[lookup])
            newVar = {
              type: 'Variable',
              datatype,
              name: nodes[i].targets[0].id.v,
              methods: details.methods,
              properties: details.properties
            }
          }
          this.vars[nodes[i].targets[0].id.v] = newVar
        } else if (nodes[i].targets[0] instanceof this.Sk.astnodes.Attribute) {
          if (nodes[i].targets[0].attr.v.slice(0, 1) !== '_') {
            this.vars[nodes[i].targets[0].value.id.v].properties.push(
              nodes[i].targets[0].attr.v
            )
          }
        } else if (nodes[i].targets[0] instanceof this.Sk.astnodes.Subscript) {
          if (nodes[i].targets[0].slice.value instanceof this.Sk.astnodes.Str) {
            this.vars[nodes[i].targets[0].value.id.v].keys.push('\'' + nodes[i].targets[0].slice.value.s.v + '\'')
          } else if (nodes[i].targets[0].slice.value instanceof this.Sk.astnodes.Num) {
            this.vars[nodes[i].targets[0].value.id.v].keys.push(nodes[i].targets[0].slice.value.n.v)
          } else if (nodes[i].targets[0].slice.value instanceof this.Sk.astnodes.NameConstant) {
            this.vars[nodes[i].targets[0].value.id.v].keys.push(nodes[i].targets[0].slice.value.value.v)
          }
        }
      } else if (nodes[i] instanceof this.Sk.astnodes.ClassDef) {
        const methodArgs = []
        const className = nodes[i].name.v
        const classMethods = []
        const properties = []
        if (nodes[i].bases.length > 0) {
          for (const method of this.classes[nodes[i].bases[0].id.v].methods) {
            classMethods.push(method)
          }
          for (const prop of this.classes[nodes[i].bases[0].id.v].properties) {
            properties.push(prop)
          }
        }
        const methods = nodes[i].body
        for (let j = 0; j < methods.length; j++) {
          if (!methods[j].name.v.startsWith('__')) {
            classMethods.push(methods[j].name.v)
          }
          if (level === 1) {
            let endLineNo
            if (j + 1 === methods.length) {
              if (i + 1 === nodes.length) {
                endLineNo = 1000000
              } else {
                endLineNo = nodes[i + 1].lineno
              }
            } else {
              endLineNo = methods[j + 1].lineno
            }
            if (lineNo >= methods[j].lineno && lineNo < endLineNo) {
              for (const arg of methods[j].args.args) {
                if (arg.arg.v !== 'self') {
                  methodArgs.push(arg.arg.v)
                }
              }
            }
          }
          for (const node of methods[j].body) {
            if (node instanceof this.Sk.astnodes.Assign) {
              if (node.targets[0] instanceof this.Sk.astnodes.Attribute) {
                if (node.targets[0].attr.v.slice(0, 1) !== '_') {
                  properties.push(node.targets[0].attr.v)
                }
              }
            }
            // Add local variables if in scope
          }
        }
        if (level === 1 && (i + 1 === nodes.length || nodes[i + 1].lineno > lineNo)) {
          this.vars.self = {
            type: className,
            methods: [...classMethods],
            properties: [...properties]
          }
        }
        for (const arg of methodArgs) {
          this.vars[arg] = {
            type: 'Parameter',
            datatype: 'unknown',
            name: arg
          }
        }
        const signature = this.getConstructorArgsFromClassDef(nodes[i])
        const isException = false
        this.classes[className] = {
          methods: classMethods,
          properties,
          signature,
          isException
        }
      } else if (nodes[i] instanceof this.Sk.astnodes.FunctionDef) {
        if (level === 1) {
          let endLineNo
          if (i + 1 === nodes.length) {
            endLineNo = 1000000
          } else {
            endLineNo = nodes[i + 1].lineno
          }
          let signature = '('
          let firstParam = true
          for (const arg of nodes[i].args.args) {
            if (lineNo >= nodes[i].lineno && lineNo < endLineNo) {
              this.vars[arg.arg.v] = {
                type: 'Parameter',
                datatype: 'unknown',
                name: arg.arg.v
              }
            }
            if (firstParam) {
              signature = signature + arg.arg.v
              firstParam = false
            } else {
              signature = signature + ', ' + arg.arg.v
            }
          }
          signature = signature + ')'
          if (!(lineNo >= nodes[i].lineno && lineNo < endLineNo)) {
            this.functions[nodes[i].name.v] = {
              signature,
              doc: 'Function'
            }
          }
        }
      }
    }
  }

  getCompletions (level = 1, lineNo = 10000000) {
    if (level === 1) {
      this.imports = []
    }
    this.Sk.configure({
      __future__: this.Sk.python3
    })
    try {
      const parse = this.Sk.parse(
        'autocompleter',
        this.code
      )
      const ast = this.Sk.astFromParse(
        parse.cst,
        'autocompleter',
        parse.flags
      )

      // Get Builtins
      for (const [name, val] of Object.entries(this.Sk.builtins)) {
        // Skip dunder names and internal variables
        if (name.startsWith('__') || name.startsWith('_')) {
          continue
        }
        let lookup = 'basic datatype not found'
        let datatype
        if (val instanceof this.Sk.builtin.bool) {
          datatype = 'bool'
          lookup = 'bool'
        } else if (val instanceof this.Sk.builtin.int_) {
          datatype = 'int'
          lookup = 'int_$rw$'
        } else if (val instanceof this.Sk.builtin.float_) {
          datatype = 'float'
          lookup = 'float_$rw$'
        } else if (val instanceof this.Sk.builtin.str) {
          datatype = 'str'
          lookup = 'str$rw$'
        } else if (typeof val?.$meth === 'function') {
          const args = this.getFunctionArgsFromTextSig(val)
          const signature = (`(${args.join(', ')})`)
          const doc = val?.$doc || 'Function'
          this.functions[name] = {
            signature,
            doc
          }
        } else if (typeof val === 'function') {
          let modName = name
          if (name === 'int_$rw$') {
            modName = 'int'
          } else if (name === 'float_$rw$') {
            modName = 'float'
          }

          const isException = val.prototype instanceof this.Sk.builtin.BaseException
          const details = this.getPrototypeDetails(val)
          this.classes[modName] = {
            ...details,
            signature: '()',
            isException
          }
        }
        if (lookup !== 'basic datatype not found') {
          const details = this.getPrototypeDetails(this.Sk.builtins[lookup])
          this.builtinVars[name] = {
            type: 'Builtin Variable',
            datatype,
            name,
            methods: details.methods,
            properties: details.properties
          }
        }
      }

      const nodes = ast.body
      // console.log(nodes)
      this.processNodes(nodes, level, lineNo)
    } catch (err) {
      // console.log(err)
      return false
    }

    const mergedVars = {
      ...this.builtinVars,
      ...this.vars
    }
    return {
      vars: mergedVars,
      classes: this.classes,
      functions: this.functions
    }
  }
}
