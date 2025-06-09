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
    const target = node.targets[0]
    if (target instanceof this.Sk.astnodes.Name) {
      this.handleNameAssignment(target, node.value)
    } else if (target instanceof this.Sk.astnodes.Attribute) {
      this.handleAttributeAssignment(target)
    } else if (target instanceof this.Sk.astnodes.Subscript) {
      this.handleSubscriptAssignment(target)
    }
  }

  handleNameAssignment (target, value) {
    const name = target.id.v
    let newVar

    if (value instanceof this.Sk.astnodes.Dict) {
      newVar = this.createDictVar(name, value)
    } else if (value instanceof this.Sk.astnodes.Call) {
      newVar = this.createCallVar(name, value)
    } else {
      newVar = this.createLiteralVar(name, value)
    }

    this.vars[name] = newVar
  }

  handleAttributeAssignment (target) {
    if (target.attr.v.slice(0, 1) !== '_') {
      this.vars[target.value.id.v].properties.push(target.attr.v)
    }
  }

  handleSubscriptAssignment (target) {
    const key = target.slice.value
    let keyVal
    if (key instanceof this.Sk.astnodes.Str) {
      keyVal = `'${key.s.v}'`
    } else if (key instanceof this.Sk.astnodes.Num) {
      keyVal = key.n.v
    } else if (key instanceof this.Sk.astnodes.NameConstant) {
      keyVal = key.value.v
    }
    this.vars[target.value.id.v].keys.push(keyVal)
  }

  createDictVar (name, value) {
    const keys = value.keys
      .map(key => {
        if (key instanceof this.Sk.astnodes.Str) return `'${key.s.v}'`
        if (key instanceof this.Sk.astnodes.Num) return key.n.v
        if (key instanceof this.Sk.astnodes.NameConstant) return key.value.v
        return undefined
      })
      .filter(k => k !== undefined)
    const details = this.getPrototypeDetails(this.Sk.builtins.dict)
    return {
      type: 'dict',
      datatype: 'dict',
      name,
      keys,
      methods: details.methods,
      properties: details.properties,
      source: 'user'
    }
  }

  createCallVar (name, value) {
    const calledName = value.func.id.v
    if (calledName === 'dict') {
      const keys = []

      if (value.args.length > 0 && value.args[0] instanceof this.Sk.astnodes.Dict) {
        for (const key of value.args[0].keys) {
          if (key instanceof this.Sk.astnodes.Str) keys.push(`'${key.s.v}'`)
          else if (key instanceof this.Sk.astnodes.Num) keys.push(key.n.v)
          else if (key instanceof this.Sk.astnodes.NameConstant) keys.push(key.value.v)
        }
      } else if (value.keywords.length > 0) {
        for (const keyword of value.keywords) {
          keys.push(`'${keyword.arg.v}'`)
        }
      }

      const details = this.getPrototypeDetails(this.Sk.builtins.dict)
      return {
        type: 'dict',
        datatype: 'dict',
        name,
        keys,
        methods: details.methods,
        properties: details.properties,
        source: 'user'
      }
    }
    if (calledName === 'set') {
      const details = this.getPrototypeDetails(this.Sk.builtins.set)
      return {
        type: 'set',
        datatype: 'set',
        name,
        methods: details.methods,
        properties: details.properties,
        source: 'user'
      }
    }
    if (calledName in this.classes) {
      return {
        type: calledName,
        datatype: calledName,
        name,
        methods: [...this.classes[calledName].methods],
        properties: [...this.classes[calledName].properties],
        source: 'user'
      }
    }
    return { type: 'Variable', datatype: 'Unknown', name, source: 'user' }
  }

  createLiteralVar (name, value) {
    let datatype = 'Unknown'
    let lookup = ''

    if (value instanceof this.Sk.astnodes.Num) {
      datatype = Number.isInteger(value.n.v) ? 'int' : 'float'
      lookup = datatype === 'int' ? 'int_$rw$' : 'float_$rw$'
    } else if (value instanceof this.Sk.astnodes.Str) {
      datatype = 'str'
      lookup = 'str'
    } else if (value instanceof this.Sk.astnodes.List) {
      datatype = 'list'
      lookup = 'list'
    } else if (value instanceof this.Sk.astnodes.Tuple) {
      datatype = 'tuple'
      lookup = 'tuple'
    } else if (value instanceof this.Sk.astnodes.Set) {
      datatype = 'set'
      lookup = 'set'
    } else if (value instanceof this.Sk.astnodes.NameConstant) {
      if (value.value.v === 1 || value.value.v === 0) {
        datatype = 'bool'
        lookup = 'bool'
      }
    }

    const details = this.getPrototypeDetails(this.Sk.builtins[lookup])
    return {
      type: 'Variable',
      datatype,
      name,
      methods: details.methods,
      properties: details.properties,
      source: 'user'
    }
  }

  handleClassDef (node, level, lineNo, nodes, index) {
    const methodArgs = []
    const className = node.name.v
    const classMethods = []
    const properties = []

    if (node.bases.length > 0) {
      const base = this.classes[node.bases[0].id.v]
      if (base) {
        classMethods.push(...base.methods)
        properties.push(...base.properties)
      }
    }

    const methods = node.body
    for (let j = 0; j < methods.length; j++) {
      const method = methods[j]

      if (!method.name.v.startsWith('__')) {
        classMethods.push(method.name.v)
      }

      if (level === 1) {
        const endLineNo = this.getMethodEndLine(methods, j, nodes, index)
        if (lineNo >= method.lineno && lineNo < endLineNo) {
          for (const arg of method.args.args) {
            if (arg.arg.v !== 'self') {
              methodArgs.push(arg.arg.v)
            }
          }
        }
      }

      for (const stmt of method.body) {
        if (stmt instanceof this.Sk.astnodes.Assign) {
          const target = stmt.targets[0]
          if (target instanceof this.Sk.astnodes.Attribute && !target.attr.v.startsWith('_')) {
            properties.push(target.attr.v)
          }
        }
      }
    }

    if (level === 1 && (index + 1 === nodes.length || nodes[index + 1].lineno > lineNo)) {
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

  getMethodEndLine (methods, j, nodes, index) {
    if (j + 1 === methods.length) {
      if (index + 1 === nodes.length) return 1000000
      return nodes[index + 1].lineno
    }
    return methods[j + 1].lineno
  }

  handleFunctionDef (node, level, lineNo, nodes, index) {
    if (level !== 1) return

    const startLine = node.lineno
    const endLine = index + 1 === nodes.length ? 1000000 : nodes[index + 1].lineno
    const isInScope = lineNo >= startLine && lineNo < endLine

    const args = node.args.args.map(arg => arg.arg.v)
    if (isInScope) {
      for (const argName of args) {
        this.vars[argName] = {
          type: 'Parameter',
          datatype: 'unknown',
          name: argName,
          source: 'user'
        }
      }
    } else {
      const signature = `(${args.join(', ')})`
      this.functions[node.name.v] = {
        signature,
        doc: 'Function',
        source: 'user'
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
