export class PyAngeloWordCompleter {
  constructor (autocompleter) {
    this.autocompleter = autocompleter
    this.completions = {
      vars: [],
      classes: {}
    }
    // this.identifierRegexps = [/[a-zA-Z_0-9$\-\u00A2-\uFFFF]/, /[a-zA-Z_0-9]+.$/]
    this.identifierRegexps = [/[a-zA-Z_0-9]*[.[]/]
  }

  setCode (code) {
    this.autocompleter.setCode(code)
  }

  updateSuggestions (pos) {
    const completions = this.autocompleter.getCompletions(1, pos.row)
    if (completions) {
      this.completions = completions
    }
    // console.log(this.completions)
  }

  getCompletions (editor, session, pos, prefix, callback) {
    editor.completer.exactMatch = true
    this.updateSuggestions(pos)

    const curLine = (session.getDocument().getLine(pos.row)).trim()
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
                meta: 'Method'
              }
            )
          }
          for (const prop of this.completions.vars[lastToken].properties) {
            this.wordList.push(
              {
                caption: '.' + prop,
                value: '.' + prop,
                meta: 'attribute'
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
        if (this.completions.vars[lastToken].type === 'Dict') {
          for (const key of this.completions.vars[lastToken].keys) {
            this.wordList.push(
              {
                caption: '[' + key + ']',
                value: '[' + key + ']',
                meta: 'Dict'
              }
            )
          }
        }
      }
    } else {
      this.wordList = [
        {
          caption: 'setCanvasSize(width, height)',
          value: 'setCanvasSize()',
          meta: 'Sets the canvas size'
        },
        {
          caption: 'noCanvas()',
          value: 'noCanvas()',
          meta: 'Removes the canvas'
        },
        {
          caption: 'focusCanvas()',
          value: 'focusCanvas()',
          meta: 'Put the focus on the canvas'
        },
        {
          caption: 'rect(x, y, width, height)',
          value: 'rect()',
          meta: 'Draw a rectangle'
        },
        {
          caption: 'circle(x, y, radius)',
          value: 'circle()',
          meta: 'Draw a circle'
        },
        {
          caption: 'ellipse(x, y, radiusX, radiusY)',
          value: 'ellipse()',
          meta: 'Draw an ellipse'
        },
        {
          caption: 'arc(x, y, radiusX, radiusY, startAngle, endAngle)',
          value: 'arc()',
          meta: 'Draw an arc'
        },
        {
          caption: 'line(x1, y1, x2, y2)',
          value: 'line()',
          meta: 'Draw a line'
        },
        {
          caption: 'point(x, y)',
          value: 'point()',
          meta: 'Draw a point'
        },
        {
          caption: 'triangle(x1, y1, x2, y2, x3, y3)',
          value: 'triangle()',
          meta: 'Draw a triangle'
        },
        {
          caption: 'quad(x1, y1, x2, y2, x3, y3, x4, y4)',
          value: 'quad()',
          meta: 'Draw a quad'
        },
        {
          caption: 'rectMode(mode)',
          value: 'rectMode()',
          meta: 'Specify method of drawing rectangles'
        },
        {
          caption: 'circleMode(mode)',
          value: 'circleMode()',
          meta: 'Specify method of drawing circles'
        },
        {
          caption: 'strokeWeight(weight)',
          value: 'strokeWeight()',
          meta: 'Set the stroke weight'
        },
        {
          caption: 'beginShape()',
          value: 'beginShape()',
          meta: 'Start recording vertices'
        },
        {
          caption: 'vertex(x, y)',
          value: 'vertex()',
          meta: 'Add a vertex at the specified point'
        },
        {
          caption: 'endShape(mode)',
          value: 'endShape()',
          meta: 'Draw the shape'
        },
        {
          caption: 'background(r, g, b, a)',
          value: 'background()',
          meta: 'clears the background'
        },
        {
          caption: 'fill(r, g, b, a)',
          value: 'fill()',
          meta: 'Set colour of shapes'
        },
        {
          caption: 'noFill()',
          value: 'noFill()',
          meta: 'Do not colour shapes'
        },
        {
          caption: 'stroke(r, g, b, a)',
          value: 'stroke()',
          meta: 'Set stroke colour'
        },
        {
          caption: 'noStroke()',
          value: 'noStroke()',
          meta: 'Do not draw outline'
        },
        {
          caption: 'isKeyPressed(code)',
          value: 'isKeyPressed()',
          meta: 'Check if key is pressed'
        },
        {
          caption: 'wasKeyPressed(code)',
          value: 'wasKeyPressed()',
          meta: 'Check if key was pressed'
        },
        {
          caption: 'wasMousePressed()',
          value: 'wasMousePressed()',
          meta: 'Check if mouse was pressed'
        },
        {
          caption: 'text(text, x, y, fontSize, fontName)',
          value: 'text()',
          meta: 'Draw text'
        },
        {
          caption: 'Image(file)',
          value: 'Image()',
          meta: 'Load an image'
        },
        {
          caption: 'drawImage(image, x, y, width, height, opacity)',
          value: 'drawImage()',
          meta: 'Draw an image'
        },
        {
          caption: 'angleMode(mode)',
          value: 'angleMode()',
          meta: 'Set the angle mode'
        },
        {
          caption: 'translate(x, y)',
          value: 'translate()',
          meta: 'Moves the position of the origin'
        },
        {
          caption: 'rotate(angle)',
          value: 'rotate()',
          meta: 'Rotate the canvas'
        },
        {
          caption: 'applyMatrix(a, b, c, d, e, f)',
          value: 'applyMatrix()',
          meta: 'Scale, rotate, and skew the current context'
        },
        {
          caption: 'shearX(angle)',
          value: 'shearX()',
          meta: 'Shear around the x-axis'
        },
        {
          caption: 'shearY(angle)',
          value: 'shearY()',
          meta: 'Shear around the y-axis'
        },
        {
          caption: 'saveState()',
          value: 'saveState()',
          meta: 'Save current settings'
        },
        {
          caption: 'restoreState()',
          value: 'restoreState()',
          meta: 'Restore current settings'
        },
        {
          caption: 'setConsoleSize(size)',
          value: 'setConsoleSize()',
          meta: 'Set the console size'
        },
        {
          caption: 'setTextSize(size)',
          value: 'setTextSize()',
          meta: 'Set the text size'
        },
        {
          caption: 'setTextColour(colour)',
          value: 'setTextColour()',
          meta: 'Set the text colour'
        },
        {
          caption: 'setHighlightColour(colour)',
          value: 'setHighlightColour()',
          meta: 'Set the highlight colour'
        },
        {
          caption: 'clear(colour)',
          value: 'clear()',
          meta: 'Clear the console'
        },
        {
          caption: 'sleep(delay)',
          value: 'sleep()',
          meta: 'Sleep for specified number of seconds'
        },
        {
          caption: 'loadSound(filename)',
          value: 'loadSound()',
          meta: 'Load a sound'
        },
        {
          caption: 'playSound(sound, loop, volume)',
          value: 'playSound()',
          meta: 'Play a sound'
        },
        {
          caption: 'stopSound(sound)',
          value: 'stopSound()',
          meta: 'Stop a sound'
        },
        {
          caption: 'pauseSound(sound)',
          value: 'pauseSound()',
          meta: 'Pause a sound'
        },
        {
          caption: 'stopAlSounds()',
          value: 'stopAllSounds()',
          meta: 'Stop all sounds'
        },
        {
          caption: 'dist(x1, y1, x2, y2)',
          value: 'dist()',
          meta: 'Distance between two points'
        },
        // Python Keywords
        {
          caption: 'and',
          value: 'and',
          meta: 'keyword'
        },
        {
          caption: 'as',
          value: 'as',
          meta: 'keyword'
        },
        {
          caption: 'assert',
          value: 'assert',
          meta: 'keyword'
        },
        {
          caption: 'break',
          value: 'break',
          meta: 'keyword'
        },
        {
          caption: 'class',
          value: 'class',
          meta: 'keyword'
        },
        {
          caption: 'continue',
          value: 'continue',
          meta: 'keyword'
        },
        {
          caption: 'def',
          value: 'def',
          meta: 'keyword'
        },
        {
          caption: 'del',
          value: 'del',
          meta: 'keyword'
        },
        {
          caption: 'elif',
          value: 'elif',
          meta: 'keyword'
        },
        {
          caption: 'else',
          value: 'else',
          meta: 'keyword'
        },
        {
          caption: 'except',
          value: 'except',
          meta: 'keyword'
        },
        {
          caption: 'False',
          value: 'False',
          meta: 'keyword'
        },
        {
          caption: 'finally',
          value: 'finally',
          meta: 'keyword'
        },
        {
          caption: 'for',
          value: 'for',
          meta: 'keyword'
        },
        {
          caption: 'forever',
          value: 'forever',
          meta: 'keyword'
        },
        {
          caption: 'from',
          value: 'from',
          meta: 'keyword'
        },
        {
          caption: 'global',
          value: 'global',
          meta: 'keyword'
        },
        {
          caption: 'if',
          value: 'if',
          meta: 'keyword'
        },
        {
          caption: 'import',
          value: 'import',
          meta: 'keyword'
        },
        {
          caption: 'in',
          value: 'in',
          meta: 'keyword'
        },
        {
          caption: 'is',
          value: 'is',
          meta: 'keyword'
        },
        {
          caption: 'lambda',
          value: 'lambda',
          meta: 'keyword'
        },
        {
          caption: 'None',
          value: 'None',
          meta: 'keyword'
        },
        {
          caption: 'nonlocal',
          value: 'nonlocal',
          meta: 'keyword'
        },
        {
          caption: 'not',
          value: 'not',
          meta: 'keyword'
        },
        {
          caption: 'or',
          value: 'or',
          meta: 'keyword'
        },
        {
          caption: 'pass',
          value: 'pass',
          meta: 'keyword'
        },
        {
          caption: 'raise',
          value: 'raise',
          meta: 'keyword'
        },
        {
          caption: 'return',
          value: 'return',
          meta: 'keyword'
        },
        {
          caption: 'True',
          value: 'True',
          meta: 'keyword'
        },
        {
          caption: 'try',
          value: 'try',
          meta: 'keyword'
        },
        {
          caption: 'while',
          value: 'while',
          meta: 'keyword'
        },
        {
          caption: 'with',
          value: 'with',
          meta: 'keyword'
        },
        {
          caption: 'yield',
          value: 'yield',
          meta: 'keyword'
        }
      ]
      // Builtin Variables
      this.wordList.push(
        {
          caption: 'width',
          value: 'width',
          meta: 'Builtin variable'
        }
      )
      this.wordList.push(
        {
          caption: 'height',
          value: 'height',
          meta: 'Builtin variable'
        }
      )
      this.wordList.push(
        {
          caption: 'windowWidth',
          value: 'windowWidth',
          meta: 'Builtin variable'
        }
      )
      this.wordList.push(
        {
          caption: 'windowHeight',
          value: 'windowHeight',
          meta: 'Builtin variable'
        }
      )
      this.wordList.push(
        {
          caption: 'mouseX',
          value: 'mouseX',
          meta: 'Builtin variable'
        }
      )
      this.wordList.push(
        {
          caption: 'mouseY',
          value: 'mouseY',
          meta: 'Builtin variable'
        }
      )
      this.wordList.push(
        {
          caption: 'mouseIsPressed',
          value: 'mouseIsPressed',
          meta: 'Builtin variable'
        }
      )

      // Add Classes to wordList
      for (const className in this.completions.classes) {
        this.wordList.push(
          {
            caption: className,
            value: className,
            meta: 'Class'
          }
        )
      }
      // Add variables to wordList
      for (const varName in this.completions.vars) {
        this.wordList.push(
          {
            caption: varName,
            value: varName,
            meta: this.completions.vars[varName].datatype + ' ' + this.completions.vars[varName].type
          }
        )
        if (this.completions.vars[varName].type === 'Dict') {
          for (const key of this.completions.vars[varName].keys) {
            this.wordList.push(
              {
                caption: varName + '[' + key + ']',
                value: varName + '[' + key + ']',
                meta: 'Dict'
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
                meta: 'Method'
              }
            )
          }
          for (const prop of this.completions.vars[varName].properties) {
            this.wordList.push(
              {
                caption: varName + '.' + prop,
                value: varName + '.' + prop,
                meta: 'attribute'
              }
            )
          }
        }
      }
      // Add functions to wordList
      for (const func in this.completions.functions) {
        this.wordList.push(
          {
            caption: func + this.completions.functions[func],
            value: func + '()',
            meta: 'Function'
          }
        )
      }
    }

    callback(null, [...this.wordList.map(function (word) {
      return {
        caption: word.caption,
        value: word.value,
        meta: word.meta,
        completer: {
          insertMatch: function (editor, data) {
            if (editor.completer.completions.filterText) {
              const ranges = editor.selection.getAllRanges()
              for (let i = 0, range; (range = ranges[i]); i++) {
                // if (editor.completer.completions.filterText.slice(0, 2) === '[\'') {
                if (editor.completer.completions.filterText.includes('[\'')) {
                  range.end.column += 2
                } else if (editor.completer.completions.filterText.includes('[')) {
                // else if (editor.completer.completions.filterText.slice(0, 1) === '[') {
                  range.end.column += 1
                }
                range.start.column -= editor.completer.completions.filterText.length
                editor.session.remove(range)
              }
            }
            editor.execCommand('insertstring', data.value || data)
            if (data.value.slice(-2) === '()') {
              const curPos = editor.getCursorPosition()
              editor.gotoLine(curPos.row + 1, curPos.column - 1)
            } else if (editor.completer.completions.filterText.slice(0, 1) === '[') {
              editor.gotoLine(pos.row + 1, pos.column + data.value.length)
            }
          }
        }
      }
    })])
  }
}
