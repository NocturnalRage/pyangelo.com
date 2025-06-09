export class Autocompleter {
  constructor (Sk) {
    this.Sk = Sk
    this.imports = []
    this.resetState()
    this.resetBuiltinState()
    this.Sk.configure({ __future__: this.Sk.python3 })
  }

  resetState () {
    this.vars = {}
    this.functions = {}
    this.classes = {}
  }

  resetBuiltinState () {
    this.builtinVars = {}
    this.builtinFunctions = {}
    this.builtinClasses = {}
  }

  setCode (code) {
    this.code = code
  }

  getCompletions (level = 1, lineNo = 10000000) {
    if (level === 1) {
      this.imports = []
    }

    // 🆕 Lazy-load built-ins once
    if (Object.keys(this.builtinVars).length === 0) {
      this.loadBuiltinVars()
    }

    try {
      const parse = this.Sk.parse('autocompleter', this.code)
      const ast = this.Sk.astFromParse(parse.cst, 'autocompleter', parse.flags)
      const nodes = ast.body
      // console.log(nodes)
      this.processNodes(nodes, level, lineNo)
    } catch (err) {
      // console.log(err)
      return false
    }

    return {
      vars: {
        ...this.builtinVars,
        ...this.vars
      },
      classes: {
        ...this.builtinClasses,
        ...this.classes
      },
      functions: {
        ...this.builtinFunctions,
        ...this.functions
      }
    }
  }

  processNodes (nodes, level, lineNo) {
    for (let i = 0; i < nodes.length; i++) {
      if (nodes[i].lineno > lineNo) {
        break
      }
      const node = nodes[i]

      if (this.isWhileOrForeverNode(node)) {
        this.processNodes(node.body, level, lineNo)
      } else if (node instanceof this.Sk.astnodes.If) {
        this.processNodes(node.body, level, lineNo)
        this.processNodes(node.orelse, level, lineNo)
      } else if (node instanceof this.Sk.astnodes.For) {
        this.handleForNode(node, level, lineNo)
      } else if (nodes[i] instanceof this.Sk.astnodes.ImportFrom) {
        this.handleImportFrom(node, level)
      } else if (nodes[i] instanceof this.Sk.astnodes.Assign) {
        this.handleAssignment(node)
      } else if (nodes[i] instanceof this.Sk.astnodes.ClassDef) {
        this.handleClassDef(node, level, lineNo, nodes, i)
      } else if (nodes[i] instanceof this.Sk.astnodes.FunctionDef) {
        this.handleFunctionDef(node, level, lineNo, nodes, i)
      }
    }
  }

  isWhileOrForeverNode (node) {
    return node instanceof this.Sk.astnodes.While || node instanceof this.Sk.astnodes.Forever
  }

  handleForNode (node, level, lineNo) {
    const datatype = this.inferForLoopVarType(node)
    const lookup = this.getLookupFromDatatype(datatype)
    const details = this.getPrototypeDetails(this.Sk.builtins[lookup])

    this.vars[node.target.id.v] = {
      type: 'Variable',
      datatype,
      name: node.target.id.v,
      methods: details.methods,
      properties: details.properties,
      source: 'user'
    }

    this.processNodes(node.body, level, lineNo)
    this.processNodes(node.orelse, level, lineNo)
  }

  handleImportFrom (node, level) {
    if (this.imports.includes(node.module.v)) return
    const paths = [
      `./${node.module.v}.py`,
      `src/lib/${node.module.v}.py`
    ]

    for (const path of paths) {
      if (path in this.Sk.builtinFiles.files) {
        this.setCode(this.Sk.builtinFiles.files[path])
        this.imports.push(node.module.v)
        this.getCompletions(level + 1)
        return
      }
    }
    console.log('Did not parse module: ' + node.module.v)
  }

  handleAssignment (node) {
    let newVar
    if (node.targets[0] instanceof this.Sk.astnodes.Name) {
      if (node.value instanceof this.Sk.astnodes.Dict) {
        const keys = []
        for (const key of node.value.keys) {
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
          name: node.targets[0].id.v,
          keys,
          methods: details.methods,
          properties: details.properties
        }
      } else if (node.value instanceof this.Sk.astnodes.Call) {
        const calledName = node.value.func.id.v
        if (calledName === 'dict') {
          const keys = []
          if (node.value.args.length > 0) {
            if (node.value.args[0] instanceof this.Sk.astnodes.Dict) {
              for (const key of node.value.args[0].keys) {
                if (key instanceof this.Sk.astnodes.Str) {
                  keys.push('\'' + key.s.v + '\'')
                } else if (key instanceof this.Sk.astnodes.Num) {
                  keys.push(key.n.v)
                } else if (key instanceof this.Sk.astnodes.NameConstant) {
                  keys.push(key.value.v)
                }
              }
            }
          } else if (node.value.keywords.length > 0) {
            for (const keyword of node.value.keywords) {
              keys.push('\'' + keyword.arg.v + '\'')
            }
          }
          const details = this.getPrototypeDetails(this.Sk.builtins.dict)
          newVar = {
            type: 'dict',
            datatype: 'dict',
            name: node.targets[0].id.v,
            keys,
            methods: details.methods,
            properties: details.properties
          }
        } else if (calledName === 'set') {
          const details = this.getPrototypeDetails(this.Sk.builtins.set)
          newVar = {
            type: 'set',
            datatype: 'set',
            name: node.targets[0].id.v,
            methods: details.methods,
            properties: details.properties
          }
        } else if (calledName in this.classes) {
          newVar = {
            type: node.value.func.id.v,
            datatype: calledName,
            name: node.targets[0].id.v,
            methods: [...this.classes[node.value.func.id.v].methods],
            properties: [...this.classes[node.value.func.id.v].properties]
          }
        } else {
          newVar = {
            type: 'Variable',
            datatype: 'Unknown',
            name: node.targets[0].id.v
          }
        }
      } else {
        let datatype
        let lookup
        if (node.value instanceof this.Sk.astnodes.Num) {
          datatype = 'float'
          lookup = 'float_$rw$'
          if (Number.isInteger(node.value.n.v)) {
            datatype = 'int'
            lookup = 'int_$rw$'
          }
        } else if (node.value instanceof this.Sk.astnodes.Str) {
          datatype = 'str'
          lookup = 'str'
        } else if (node.value instanceof this.Sk.astnodes.List) {
          datatype = 'list'
          lookup = 'list'
        } else if (node.value instanceof this.Sk.astnodes.Tuple) {
          datatype = 'tuple'
          lookup = 'tuple'
        } else if (node.value instanceof this.Sk.astnodes.Set) {
          datatype = 'set'
          lookup = 'set'
        } else if (node.value instanceof this.Sk.astnodes.NameConstant) {
          if (node.value.value.v === 1 || node.value.value.v === 0) {
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
          name: node.targets[0].id.v,
          methods: details.methods,
          properties: details.properties,
          source: 'user'
        }
      }
      this.vars[node.targets[0].id.v] = newVar
    } else if (node.targets[0] instanceof this.Sk.astnodes.Attribute) {
      if (node.targets[0].attr.v.slice(0, 1) !== '_') {
        this.vars[node.targets[0].value.id.v].properties.push(
          node.targets[0].attr.v
        )
      }
    } else if (node.targets[0] instanceof this.Sk.astnodes.Subscript) {
      if (node.targets[0].slice.value instanceof this.Sk.astnodes.Str) {
        this.vars[node.targets[0].value.id.v].keys.push('\'' + node.targets[0].slice.value.s.v + '\'')
      } else if (node.targets[0].slice.value instanceof this.Sk.astnodes.Num) {
        this.vars[node.targets[0].value.id.v].keys.push(node.targets[0].slice.value.n.v)
      } else if (node.targets[0].slice.value instanceof this.Sk.astnodes.NameConstant) {
        this.vars[node.targets[0].value.id.v].keys.push(node.targets[0].slice.value.value.v)
      }
    }
  }

  handleClassDef (node, level, lineNo, nodes, i) {
    const methodArgs = []
    const className = node.name.v
    const classMethods = []
    const properties = []
    if (node.bases.length > 0) {
      for (const method of this.classes[node.bases[0].id.v].methods) {
        classMethods.push(method)
      }
      for (const prop of this.classes[node.bases[0].id.v].properties) {
        properties.push(prop)
      }
    }
    const methods = node.body
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
        properties: [...properties],
        source: 'user'
      }
    }
    for (const arg of methodArgs) {
      this.vars[arg] = {
        type: 'Parameter',
        datatype: 'unknown',
        name: arg,
        source: 'user'
      }
    }
    const signature = this.getConstructorArgsFromClassDef(node)
    const isException = false
    this.classes[className] = {
      methods: classMethods,
      properties,
      signature,
      isException,
      source: 'user'
    }
  }

  handleFunctionDef (node, level, lineNo, nodes, i) {
    if (level === 1) {
      let endLineNo
      if (i + 1 === nodes.length) {
        endLineNo = 1000000
      } else {
        endLineNo = nodes[i + 1].lineno
      }
      let signature = '('
      let firstParam = true
      for (const arg of node.args.args) {
        if (lineNo >= node.lineno && lineNo < endLineNo) {
          this.vars[arg.arg.v] = {
            type: 'Parameter',
            datatype: 'unknown',
            name: arg.arg.v,
            source: 'user'
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
      if (!(lineNo >= node.lineno && lineNo < endLineNo)) {
        this.functions[node.name.v] = {
          signature,
          doc: 'Function',
          source: 'user'
        }
      }
    }
  }

  getLookupFromDatatype (datatype) {
    switch (datatype) {
      case 'int': return 'int_$rw$'
      case 'float': return 'float_$rw$'
      case 'str': return 'str'
      default: return 'unknown'
    }
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

  loadBuiltinVars () {
    this.resetBuiltinState()
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
        this.builtinFunctions[name] = {
          signature,
          doc,
          source: 'builtin'
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
        this.builtinClasses[modName] = {
          ...details,
          signature: '()',
          isException,
          source: 'builtin'
        }
      }
      if (lookup !== 'basic datatype not found') {
        const details = this.getPrototypeDetails(this.Sk.builtins[lookup])
        this.builtinVars[name] = {
          type: 'Builtin Variable',
          datatype,
          name,
          methods: details.methods,
          properties: details.properties,
          source: 'builtin'
        }
      }
    }
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
}
