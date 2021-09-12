export const staticWordCompleter = {
  getCompletions: function (editor, session, pos, prefix, callback) {
    const wordList = [
      'setCanvasSize',
      'noCanvas',
      'focusCanvas',
      'rect',
      'circle',
      'ellipse',
      'arc',
      'line',
      'point',
      'triangle',
      'quad',
      'rectMode',
      'circleMode',
      'strokeWeight',
      'beginShape',
      'vertex',
      'endShape',
      'background',
      'fill',
      'noFill',
      'stroke',
      'noStroke',
      'isKeyPressed',
      'wasKeyPressed',
      'text',
      'loadImage',
      'image',
      'angleMode',
      'translate',
      'rotate',
      'applyMatrix',
      'shearX',
      'shearY',
      'saveState',
      'restoreState',
      'setConsoleSize',
      'setTextSize',
      'setTextColour',
      'setHighlightColour',
      'clear',
      'sleep',
      'loadSound',
      'playSound',
      'stopSound',
      'pauseSound',
      'stopAllSounds',
      'width',
      'height',
      'mouseX',
      'mouseY',
      'dist',
      'Sprite',
      'TextSprite',
      'RectangleSprite',
      'CircleSprite',
      'EllipseSprite'
    ]
    callback(null, [...wordList.map(function (word) {
      return {
        caption: word,
        value: word,
        meta: 'static'
      }
    }), ...session.$mode.$highlightRules.$keywordList.map(function (word) {
      return {
        caption: word,
        value: word,
        meta: 'keyword'
      }
    })])
  }
}
