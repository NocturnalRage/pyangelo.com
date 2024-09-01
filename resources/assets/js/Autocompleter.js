export class Autocompleter {
  constructor (Sk) {
    this.Sk = Sk
    this.imports = []
    this.vars = {}
    this.functions = {}
    this.classes = {}
    this.listMethods = [
      'append',
      'clear',
      'copy',
      'count',
      'extend',
      'index',
      'insert',
      'pop',
      'remove',
      'reverse',
      'sort'
    ]
    this.dictMethods = [
      'clear',
      'copy',
      'fromkeys',
      'get',
      'items',
      'keys',
      'pop',
      'popitem',
      'setdefault',
      'update',
      'values'
    ]
  }

  setCode (code) {
    this.code = code
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
        if (nodes[i].iter instanceof this.Sk.astnodes.Str) {
          this.vars[nodes[i].target.id.v] = {
            type: 'Variable',
            datatype: 'String',
            name: nodes[i].target.id.v
          }
        } else if (nodes[i].iter instanceof this.Sk.astnodes.Num) {
          this.vars[nodes[i].target.id.v] = {
            type: 'Variable',
            datatype: 'Num',
            name: nodes[i].target.id.v
          }
        } else if (nodes[i].iter instanceof this.Sk.astnodes.NameConstant) {
          this.vars[nodes[i].target.id.v] = {
            type: 'Variable',
            datatype: 'Constant',
            name: nodes[i].target.id.v
          }
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
            newVar = {
              type: 'Dict',
              datatype: 'Dict',
              name: nodes[i].targets[0].id.v,
              keys,
              methods: this.dictMethods,
              properties: []
            }
          } else if (nodes[i].value instanceof this.Sk.astnodes.Call) {
            if (nodes[i].value.func.id.v in this.classes) {
              newVar = {
                type: nodes[i].value.func.id.v,
                datatype: 'Object',
                methods: [...this.classes[nodes[i].value.func.id.v].methods],
                properties: [...this.classes[nodes[i].value.func.id.v].properties]
              }
            } else if (nodes[i].value.func.id.v === 'dict') {
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
              newVar = {
                type: 'Dict',
                datatype: 'Dict',
                name: nodes[i].targets[0].id.v,
                keys,
                methods: this.dictMethods,
                properties: []
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
            if (nodes[i].value instanceof this.Sk.astnodes.Num) {
              datatype = 'Number'
            } else if (nodes[i].value instanceof this.Sk.astnodes.Str) {
              datatype = 'String'
            } else if (nodes[i].value instanceof this.Sk.astnodes.List) {
              datatype = 'List'
            } else if (nodes[i].value instanceof this.Sk.astnodes.NameConstant) {
              datatype = 'Constant'
            } else {
              datatype = 'Unknown'
            }
            newVar = {
              type: 'Variable',
              datatype,
              name: nodes[i].targets[0].id.v
            }
            if (datatype === 'List') {
              newVar.methods = this.listMethods
              newVar.properties = []
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
        this.classes[className] = {
          methods: classMethods,
          properties
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
            type: 'Variable',
            datatype: 'Parameter',
            name: arg
          }
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
                type: 'Variable',
                datatype: 'Parameter',
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
            this.functions[nodes[i].name.v] = signature
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
      const nodes = ast.body
      // console.log(nodes)
      this.processNodes(nodes, level, lineNo)
    } catch (err) {
      // console.log(err)
      return false
    }
    return {
      vars: this.vars,
      classes: this.classes,
      functions: this.functions
    }
  }
}
