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
    this.containerElementTypes = {}
  }

  resetBuiltinState () {
    this.builtinVars = {}
    this.builtinFunctions = {}
    this.builtinClasses = {}
  }

  // Shared factory for any simple type
  _makeVar (name, datatype) {
    const lookup = this.getLookupFromDatatype(datatype)
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

  // Shared dict‐factory
  _makeDictVar (name, keys) {
    const details = this.getPrototypeDetails(this.Sk.builtins.dict)
    return {
      type: 'dict',
      datatype: 'dict',
      name,
      keys, // known keys or empty array
      methods: details.methods,
      properties: details.properties,
      source: 'user'
    }
  }

  setCode (code) {
    this.code = code
  }

  getCompletions (level = 1, lineNo = 10000000) {
    if (level === 1) {
      this.imports = []
    }

    // Lazy-load built-ins once
    if (Object.keys(this.builtinVars).length === 0) {
      this.loadBuiltinVars()
    }

    try {
      const parse = this.Sk.parse('autocompleter', this.code)
      const ast = this.Sk.astFromParse(parse.cst, 'autocompleter', parse.flags)
      const nodes = ast.body
      // this.resetState()
      this.vars = { ...this.builtinVars }
      this.classes = { ...this.builtinClasses }
      this.functions = { ...this.builtinFunctions }
      this.processNodes(nodes, level, lineNo)
    } catch (err) {
      // console.error('Autocompleter parse error:', err)
      return false
    }
    return {
      vars: { ...this.vars },
      classes: { ...this.classes },
      functions: { ...this.functions }
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
    this.vars[node.target.id.v] = this._makeVar(node.target.id.v, datatype)

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
    // console.log('Did not parse module: ' + node.module.v)
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
    return this._makeDictVar(name, keys)
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
    // Check if called function is user-defined and has known returnType
    if (this.functions[calledName]?.returnType && this.functions[calledName].returnType !== 'Unknown') {
      const returnType = this.functions[calledName].returnType
      if (returnType === 'dict') {
        return this._makeDictVar(name, [])
      }
      return this._makeVar(name, returnType)
    }
    return { type: 'Variable', datatype: 'Unknown', name, source: 'user' }
  }

  createLiteralVar (name, value) {
    // 1) Base type inference
    let datatype = this.inferTypeFromNode(value) || 'Unknown'

    // 2) Container element‐type inference (List/Tuple)
    if (value instanceof this.Sk.astnodes.List || value instanceof this.Sk.astnodes.Tuple) {
      const elementTypes = value.elts.map(e => this.inferTypeFromNode(e))
      const unique = [...new Set(elementTypes)].filter(t => t && t !== 'Unknown')
      if (unique.length === 1) {
        // store homogeneous element type for later Subscript inference
        this.containerElementTypes = this.containerElementTypes || {}
        this.containerElementTypes[name] = unique[0]
      }
    // 3) Alias inference (x = y)
    } else if (value instanceof this.Sk.astnodes.Name) {
      const ref = this.vars[value.id.v] || this.builtinVars[value.id.v]
      if (ref) {
        datatype = ref.datatype || datatype
      }
    // 4) BinOp (x = y + 1)
    } else if (value instanceof this.Sk.astnodes.BinOp) {
      const op = value.op // the operator node
      // true division always yields float
      if (op instanceof this.Sk.astnodes.Div) {
        datatype = 'float'
      } else {
        // other ops: only infer if both sides agree
        const l = this.inferTypeFromNode(value.left)
        const r = this.inferTypeFromNode(value.right)
        if (l && l === r) {
          datatype = l
        }
      }
    // 5) Subscript (x = a[0])
    } else if (value instanceof this.Sk.astnodes.Subscript) {
      const listName = value.value?.id?.v
      const info = this.vars[listName]
      if (info && ['list', 'tuple'].includes(info.datatype)) {
        const elem = this.containerElementTypes?.[listName]
        if (elem) datatype = elem
      }
    // 6) Ternary (x = A if cond else B)
    } else if (value instanceof this.Sk.astnodes.IfExp) {
      const tA = this.inferTypeFromNode(value.body)
      const tB = this.inferTypeFromNode(value.orelse)
      if (tA && tA === tB) datatype = tA
    }

    // 7) Finally, build the VarInfo using your shared factory
    return this._makeVar(name, datatype)
  }

  handleClassDef (node, level, lineNo, nodes, index) {
    const methodArgs = []
    const className = node.name.v
    const classMethods = []
    let properties = []

    // 1) Inherit base‐class methods & properties
    if (node.bases.length > 0) {
      const base = this.classes[node.bases[0].id.v]
      if (base) {
        classMethods.push(...base.methods)
        properties = properties.concat(base.properties)
      }
    }

    // 2) Only look at FunctionDef nodes
    const methods = node.body.filter(n => n instanceof this.Sk.astnodes.FunctionDef)

    for (let j = 0; j < methods.length; j++) {
      const method = methods[j]
      const name = method.name.v

      // A) @property or @setter → attribute
      const isProp = method.decorator_list.some(dec =>
        (dec.id && dec.id.v === 'property') ||
        (dec.attr && dec.attr.v === 'setter')
      )
      if (isProp) {
        properties.push(name)
        continue
      }

      // B) public method (no leading single underscore)
      if (!name.startsWith('_')) {
        classMethods.push(name)
      }

      // C) within __init__ or any method, collect its args for in‐scope completion
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

      // D) scan the method body for any self.xxx = … patterns, tuples, augassign, setattr
      for (const stmt of method.body) {
        // D1) plain or chained assignments
        if (stmt instanceof this.Sk.astnodes.Assign) {
          for (const target of stmt.targets) {
            // a) simple self.attr = …
            if (
              target instanceof this.Sk.astnodes.Attribute &&
              target.value.id.v === 'self' &&
              !target.attr.v.startsWith('_')
            ) {
              properties.push(target.attr.v)
            } else if (target instanceof this.Sk.astnodes.Tuple) {
              // b) tuple‐unpack: self.a, self.b = …
              for (const elt of target.elts) {
                if (
                  elt instanceof this.Sk.astnodes.Attribute &&
                  elt.value.id.v === 'self' &&
                  !elt.attr.v.startsWith('_')
                ) {
                  properties.push(elt.attr.v)
                }
              }
            }
          }
        } else if (stmt instanceof this.Sk.astnodes.AugAssign) {
          // D2) augmented assign (self.count += …)
          const t = stmt.target
          if (
            t instanceof this.Sk.astnodes.Attribute &&
            t.value.id.v === 'self' &&
            !t.attr.v.startsWith('_')
          ) {
            properties.push(t.attr.v)
          }
        } else if (
          // D3) setattr(self, 'dyn', …)
          stmt instanceof this.Sk.astnodes.Expr &&
          stmt.value instanceof this.Sk.astnodes.Call &&
          stmt.value.func.id?.v === 'setattr'
        ) {
          const [obj, keyNode] = stmt.value.args
          if (
            obj instanceof this.Sk.astnodes.Name &&
            obj.id.v === 'self' &&
            keyNode instanceof this.Sk.astnodes.Str
          ) {
            properties.push(keyNode.s.v)
          }
        }
      }
    }

    // 3) If we’re inside a method, expose `self` with the final lists
    if (
      level === 1 &&
      (index + 1 === nodes.length || nodes[index + 1].lineno > lineNo)
    ) {
      this.vars.self = {
        type: className,
        methods: Array.from(new Set(classMethods)),
        properties: Array.from(new Set(properties)),
        source: 'user'
      }
    }

    // 4) Make locals for any in-scope parameters
    for (const arg of methodArgs) {
      this.vars[arg] = {
        type: 'Parameter',
        datatype: 'unknown',
        name: arg,
        source: 'user'
      }
    }

    // 5) Register the class (deduplicating properties too)
    this.classes[className] = {
      methods: Array.from(new Set(classMethods)),
      properties: Array.from(new Set(properties)),
      signature: this.getConstructorArgsFromClassDef(node),
      isException: false,
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

      // multi-return inference: collect all Return types
      const returnTypes = new Set()
      // recursive helper to walk nested bodies/orelse
      const collectReturns = stmts => {
        for (const stmt of stmts) {
          if (stmt instanceof this.Sk.astnodes.Return) {
            if (stmt.value) {
              const t = this.inferTypeFromNode(stmt.value) || 'Unknown'
              returnTypes.add(t)
            }
          }
          // walk into nested blocks
          if (stmt.body) collectReturns(stmt.body)
          if (stmt.orelse) collectReturns(stmt.orelse)
        }
      }
      collectReturns(node.body)
      // ignore Unknowns when deciding
      returnTypes.delete('Unknown')
      // if exactly one concrete type, use it; otherwise Unknown
      let returnType = 'Unknown'
      if (returnTypes.size === 1) {
        returnType = [...returnTypes][0]
      }

      this.functions[node.name.v] = {
        signature,
        doc: 'Function',
        returnType,
        source: 'user'
      }
    }
  }

  getLookupFromDatatype (datatype) {
    switch (datatype) {
      case 'int': return 'int_$rw$'
      case 'float': return 'float_$rw$'
      case 'str': return 'str'
      case 'list': return 'list'
      case 'tuple': return 'tuple'
      case 'set': return 'set'
      case 'dict': return 'dict'
      case 'bool': return 'bool'
      case 'NoneType': return 'NoneType'
      default: return 'object'
    }
  }

  inferTypeFromNode (node) {
    if (!node) return 'Unknown'

    if (node instanceof this.Sk.astnodes.List) {
      return 'list'
    } else if (node instanceof this.Sk.astnodes.Dict) {
      return 'dict'
    } else if (node instanceof this.Sk.astnodes.Tuple) {
      return 'tuple'
    } else if (node instanceof this.Sk.astnodes.Set) {
      return 'set'
    } else if (node instanceof this.Sk.astnodes.Str) {
      return 'str'
    } else if (node instanceof this.Sk.astnodes.Num) {
      return Number.isInteger(node.n.v) ? 'int' : 'float'
    } else if (node instanceof this.Sk.astnodes.NameConstant) {
      const val = node.value?.v
      if (val === 0 || val === 1) {
        return 'bool'
      } else if (val === null) {
        return 'NoneType'
      }
    } else if (node instanceof this.Sk.astnodes.Call) {
      // Function call – type will be handled elsewhere
      return 'Unknown'
    } else if (node instanceof this.Sk.astnodes.Name) {
      // Variable – try to use existing var info
      const varInfo = this.vars[node.id?.v]
      if (varInfo?.datatype) {
        return varInfo.datatype
      }
    }
    return 'Unknown'
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
    // Case: Name — first check if we know its container’s element type
    if (iter instanceof this.Sk.astnodes.Name) {
      const name = iter.id.v
      // 1) if this was a literal list/tuple with homogeneous elements, use that
      const elemType = this.containerElementTypes[name]
      if (elemType) {
        return elemType
      }
      // 2) otherwise fall back on the variable’s own datatype
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
      } else if (val instanceof this.Sk.builtins.Colour) {
        const details = this.getPrototypeDetails(this.Sk.builtins.Colour)
        this.builtinVars[name] = {
          type: 'Builtin Variable',
          datatype: 'Colour',
          name,
          methods: details.methods,
          properties: details.properties,
          source: 'builtin'
        }
        continue
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
