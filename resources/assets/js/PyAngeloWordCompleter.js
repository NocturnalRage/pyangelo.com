export class PyAngeloWordCompleter {
  constructor (autocompleter) {
    this.autocompleter = autocompleter
    this.completions = {
      vars: {},
      classes: {},
      functions: {}
    }
    this.VAR_SCORE = 1000
    this.FUNCTION_SCORE = 900
    this.METHOD_SCORE = 800
    this.ATTRIBUTE_SCORE = 700
    this.CLASS_SCORE = 500
    this.KEYWORD_SCORE = 300
    this.identifierRegexps = [/[a-zA-Z_0-9]*[.[]/]

    this.COLOUR_FUNCTIONS = [
      'setTextColour',
      'setHighlightColour',
      'clear',
      'background',
      'fill',
      'stroke',
      'Colour',
      'setColour',
      'setStroke'
    ]
  }

  setCode (code) {
    this.autocompleter.setCode(code)
  }

  updateSuggestions (pos) {
    const completions = this.autocompleter.getCompletions(1, pos.row)
    if (completions) {
      this.completions = completions
    }
  }

  getCompletions (editor, session, pos, prefix, callback) {
    editor.completer.exactMatch = true
    this.updateSuggestions(pos)

    const curLine = (session.getDocument().getLine(pos.row)).trim()
    const isInExceptClause = curLine.startsWith('except') || curLine.includes(' except ')
    const curTokens = curLine.slice(0, pos.column).split(/[\s(]+/)
    let lastToken = curTokens[curTokens.length - 1].slice(0, -1)
    if (prefix === '.') {
      this.wordList = []
      if (lastToken in this.completions.vars) {
        if ('methods' in this.completions.vars[lastToken]) {
          for (const method of this.completions.vars[lastToken].methods) {
            this.wordList.push(
              {
                caption: '.' + method + '()',
                value: '.' + method + '()',
                meta: 'Method',
                score: this.METHOD_SCORE
              }
            )
          }
        }
        if ('properties' in this.completions.vars[lastToken]) {
          for (const prop of this.completions.vars[lastToken].properties) {
            this.wordList.push(
              {
                caption: '.' + prop,
                value: '.' + prop,
                meta: 'attribute',
                score: this.ATTRIBUTE_SCORE
              }
            )
          }
        }
      }
    } else if (prefix === '[') {
      if (lastToken === '' && curTokens.length > 1) {
        lastToken = curTokens[curTokens.length - 2]
      }

      if (lastToken in this.completions.vars) {
        this.wordList = []
        if (this.completions.vars[lastToken].type === 'dict') {
          for (const key of this.completions.vars[lastToken].keys) {
            this.wordList.push(
              {
                caption: '[' + key + ']',
                value: '[' + key + ']',
                meta: 'dict',
                score: this.VAR_SCORE
              }
            )
          }
        }
      }
    } else {
      this.wordList = [
        // Python Keywords
        {
          caption: 'and',
          value: 'and',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'as',
          value: 'as',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'assert',
          value: 'assert',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'break',
          value: 'break',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'class',
          value: 'class',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'continue',
          value: 'continue',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'def',
          value: 'def',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'del',
          value: 'del',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'elif',
          value: 'elif',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'else',
          value: 'else',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'except',
          value: 'except',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'False',
          value: 'False',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'finally',
          value: 'finally',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'for',
          value: 'for',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'forever',
          value: 'forever',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'from',
          value: 'from',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'global',
          value: 'global',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'if',
          value: 'if',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'import',
          value: 'import',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'in',
          value: 'in',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'is',
          value: 'is',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'lambda',
          value: 'lambda',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'None',
          value: 'None',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'nonlocal',
          value: 'nonlocal',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'not',
          value: 'not',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'or',
          value: 'or',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'pass',
          value: 'pass',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'raise',
          value: 'raise',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'return',
          value: 'return',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'True',
          value: 'True',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'try',
          value: 'try',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'while',
          value: 'while',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'with',
          value: 'with',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        },
        {
          caption: 'yield',
          value: 'yield',
          meta: 'keyword',
          score: this.KEYWORD_SCORE
        }
      ]

      // Add Classes or Exceptions depending on context
      for (const className in this.completions.classes) {
        const cls = this.completions.classes[className]
        if (isInExceptClause) {
          if (cls.isException) {
            this.wordList.push({
              caption: className,
              value: className,
              meta: 'Exception',
              score: this.CLASS_SCORE
            })
          }
        } else {
          if (!cls.isException) {
            this.wordList.push({
              caption: className + this.completions.classes[className].signature,
              value: className + '()',
              meta: 'Class',
              score: this.CLASS_SCORE
            })
          }
        }
      }

      // Add variables to wordList
      for (const varName in this.completions.vars) {
        this.wordList.push(
          {
            caption: varName,
            value: varName,
            meta: this.completions.vars[varName].datatype + ' ' + this.completions.vars[varName].type,
            score: this.VAR_SCORE
          }
        )
        if (this.completions.vars[varName].type === 'dict') {
          for (const key of this.completions.vars[varName].keys) {
            this.wordList.push(
              {
                caption: varName + '[' + key + ']',
                value: varName + '[' + key + ']',
                meta: 'dict',
                score: this.VAR_SCORE
              }
            )
          }
        }
        if ('methods' in this.completions.vars[varName]) {
          for (const method of this.completions.vars[varName].methods) {
            this.wordList.push(
              {
                caption: varName + '.' + method + '()',
                value: varName + '.' + method + '()',
                meta: 'Method',
                score: this.METHOD_SCORE
              }
            )
          }
          for (const prop of this.completions.vars[varName].properties) {
            this.wordList.push(
              {
                caption: varName + '.' + prop,
                value: varName + '.' + prop,
                meta: 'attribute',
                score: this.ATTRIBUTE_SCORE
              }
            )
          }
        }
      }
      // Add functions to wordList
      for (const func in this.completions.functions) {
        this.wordList.push(
          {
            caption: func + this.completions.functions[func].signature,
            value: func + '()',
            meta: this.completions.functions[func].doc,
            score: this.FUNCTION_SCORE
          }
        )
      }
      // Add named colour suggestions if the line includes any target function
      const cursor = editor.getCursorPosition()
      const line = session.getDocument().getLine(cursor.row)

      // 2) how many characters the user has already typed
      const myPrefix = prefix || ''

      // 3) index of the character just before the typed prefix begins
      const startCol = cursor.column - myPrefix.length - 1
      const openChar = startCol >= 0
        ? line.charAt(startCol)
        : null

      // 4) only wrap with quotes if there isn't already ' or " there
      const needsQuotes = openChar !== '"' && openChar !== "'"

      for (const func of this.COLOUR_FUNCTIONS) {
        if (curLine.includes(func + '(')) {
          if (this.autocompleter.Sk?.Colour?.namedColours) {
            const names = Object.keys(this.autocompleter.Sk.Colour.namedColours)
            for (const name of names) {
              const insertValue = needsQuotes
                ? `'${name}'` // wrap if no quote pre-existing
                : name // bare name if already inside quotes
              this.wordList.push({
                caption: `${name}`,
                value: insertValue,
                meta: 'Named Colour',
                score: this.VAR_SCORE
              })
            }
            break // Only match one function per line
          }
        }
      }
    }

    callback(null, [...this.wordList.map(function (word) {
      return {
        caption: word.caption,
        value: word.value,
        meta: word.meta,
        score: word.score,
        completer: {
          insertMatch: function (editor, data) {
            const prefix = editor.completer.completions.filterText || ''
            const ranges = editor.selection.getAllRanges()

            for (let i = 0; i < ranges.length; i++) {
              const range = ranges[i]

              // Adjust end column for dict keys with brackets
              if (prefix.includes('[\'')) {
                range.end.column += 2
              } else if (prefix.includes('[')) {
                range.end.column += 1
              }

              // Always back up the start by prefix length
              range.start.column -= prefix.length

              // Remove the matched prefix
              editor.session.remove(range)

              // Insert the selected word
              editor.session.insert(range.start, data.value || data)
            }

            // Optional: move cursor out of ()
            if ((data.value || data).slice(-2) === '()') {
              const curPos = editor.getCursorPosition()
              editor.gotoLine(curPos.row + 1, curPos.column - 1)
            } else if (prefix.slice(0, 1) === '[') {
              // Special case: keep cursor position for dict
              editor.gotoLine(pos.row + 1, pos.column + (data.value || data).length)
            }
          }
        }
      }
    })])
  }
}
