const Sk = require('skulpt');
import { Autocompleter } from './Autocompleter';

describe('Autocompleter: Test Cases', () => {
  let a;

  beforeEach(() => {
    a = new Autocompleter(Sk);
  });

  it('can create Autocompleter', () => {
    expect(a.Sk).toBe(Sk);
  });

  it('can set code', () => {
    const code = 'print("Hello")';
    a.setCode(code);
    expect(a.code).toBe(code);
  });

  it('can detect builtin variables ', () => {
    Sk.PyAngelo.reset()
    const code = `
print('Test builtin variables are loaded')
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.DRACULA_YELLOW).toMatchObject({
      type: 'Builtin Variable',
      datatype: 'int',
      name: 'DRACULA_YELLOW',
      methods: [
        'conjugate',
        'bit_length',
        'to_bytes',
        'from_bytes',
      ],
      properties: [
        'real',
        'imag',
        'numerator',
        'denominator',
      ]
    })
    expect(completions.vars.PI).toMatchObject({
      type: 'Builtin Variable',
      datatype: 'float',
      name: 'PI',
      methods: [
        'conjugate',
        'as_integer_ratio',
        'is_integer',
      ],
      properties: [
        'real',
        'imag',
      ]
    })
    expect(completions.vars.mouseIsPressed).toMatchObject({
      type: 'Builtin Variable',
      datatype: 'bool',
      name: 'mouseIsPressed',
      methods: [],
      properties: []
    })
    expect(completions.functions.setCanvasSize).toMatchObject({
      signature: '(w, h, yAxisMode)',
      doc: 'Sets the size of the canvas that all drawings are written to. The first parameter specifies the width in pixels and the second the height. The thrid parameter specifies the direction of the y axis. The constant CARTESIAN can be used to specify the y axis acts like a regular cartesian plane in maths, and JAVASCRIPT can be used to specify a traditional javascript y-axis that moves down the screen. The default value for yAxisMode is CARTESIAN.'
    })
    expect(completions.classes.Image).toMatchObject({
      methods: [ 'draw' ], properties: [], isException: false
    })
  })

  it('can detect basic variables', () => {
    const code = `
fun = "yes"
score = 0
cost = 12.99
boring = False
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.fun).toMatchObject({
      type: 'Variable',
      datatype: 'str',
      name: 'fun',
      methods: [
        'encode',       'replace',    'split',
        'rsplit',       'join',       'capitalize',
        'title',        'center',     'count',
        'expandtabs',   'find',       'partition',
        'index',        'ljust',      'lower',
        'lstrip',       'rfind',      'rindex',
        'rjust',        'rstrip',     'rpartition',
        'splitlines',   'strip',      'swapcase',
        'upper',        'startswith', 'endswith',
        'isascii',      'islower',    'isupper',
        'istitle',      'isspace',    'isdigit',
        'isnumeric',    'isalpha',    'isalnum',
        'isidentifier', 'zfill',      'format'
      ],
      properties: []
    })
    expect(completions.vars.score).toMatchObject({
      type: 'Variable',
      datatype: 'int',
      name: 'score',
      methods: [ 'conjugate', 'bit_length', 'to_bytes', 'from_bytes' ],
      properties: [ 'real', 'imag', 'numerator', 'denominator' ]
    })
    expect(completions.vars.cost).toMatchObject({
      type: 'Variable',
      datatype: 'float',
      name: 'cost',
      methods: [ 'conjugate', 'as_integer_ratio', 'is_integer' ],
      properties: [ 'real', 'imag' ]
    })
    expect(completions.vars.boring).toMatchObject({
      type: 'Variable',
      datatype: 'bool',
      name: 'boring',
      methods: [],
      properties: []
    })
  });

  it('can detect in scope variables', () => {
    const code = `
fun = "yes"
score = 0
cool = True
`;
    a.setCode(code);
    const level = 1
    const lineNo = 2
    const completions = a.getCompletions(level, lineNo);
    expect(completions.vars.fun).toMatchObject({
      type: 'Variable',
      datatype: 'str',
      name: 'fun',
      methods: [
        'encode',       'replace',    'split',
        'rsplit',       'join',       'capitalize',
        'title',        'center',     'count',
        'expandtabs',   'find',       'partition',
        'index',        'ljust',      'lower',
        'lstrip',       'rfind',      'rindex',
        'rjust',        'rstrip',     'rpartition',
        'splitlines',   'strip',      'swapcase',
        'upper',        'startswith', 'endswith',
        'isascii',      'islower',    'isupper',
        'istitle',      'isspace',    'isdigit',
        'isnumeric',    'isalpha',    'isalnum',
        'isidentifier', 'zfill',      'format'
      ],
      properties: []
    })
    expect(completions.vars.score).toBeUndefined();
    expect(completions.vars.cool).toBeUndefined();
  });

  it('can detect classes', () => {
    const code = `
class Ball:
    def __init__(self, x, y, radius):
        self.x = x
        self.y = y
        self.radius = radius
    def draw(self):
        print(self.x)
        print(self.y)
        print(self.radius)
# Need an extra line so
# Autocompleter knows class has ended
print("Hello")
    `;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.classes.Ball).toMatchObject({
      methods: [ 'draw' ],
      properties: [ 'x', 'y', 'radius' ],
      isException: false
    })
  });

  it('can detect inherited classes', () => {
    const code = `
class Ball:
    def __init__(self, x, y, radius):
        self.x = x
        self.y = y
        self.radius = radius
        self._ignoreme = 5
    def draw(self):
        print(self.x)
        print(self.y)
        print(self.radius)
class ColourBall(Ball):
    def setColour(self, colour):
        self.colour = colour
# Need an extra line so
# Autocompleter knows class has ended
print("Hello")
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.classes.ColourBall).toMatchObject({
      methods: [ 'draw', 'setColour' ],
      properties: [ 'x', 'y', 'radius', 'colour' ],
      isException: false
    })
  });

  it('can detect objects', () => {
    const code = `
class Ball:
    def __init__(self, x, y, radius):
        self.x = x
        self.y = y
        self.radius = radius
    def draw(self):
        print(self.x)
        print(self.y)
        print(self.radius)
ball = Ball(10, 20, 5)
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.ball).toMatchObject({
      type: 'Ball',
      datatype: 'Ball',
      methods: [ 'draw' ],
      properties: [ 'x', 'y', 'radius' ]
    })
  });

  it('can detect dynamic properties added to objects', () => {
    const code = `
class Ball:
    def __init__(self, x, y, radius):
        self.x = x
        self.y = y
        self.radius = radius
    def draw(self):
        print(self.x)
        print(self.y)
        print(self.radius)
ball = Ball(10, 20, 5)
ball.owner = 'Jeff'
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.ball).toMatchObject({
      type: 'Ball',
      datatype: 'Ball',
      methods: ['draw'],
      properties: ['x', 'y', 'radius', 'owner']
    })
  });

  it('can detect list variables', () => {
    const code = `
scores = [3, 5, 6]
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.scores).toMatchObject({
      type: 'Variable',
      datatype: 'list',
      name: 'scores',
      methods: [
        'clear',   'copy',
        'append',  'insert',
        'extend',  'pop',
        'remove',  'sort',
        'index',   'count',
        'reverse'
      ],
      properties: []
    })
  });

  it('can detect dict variables', () => {
    const code = `
words = {
  1: 'Not sure',
  'big': 'of considerable size or extent',
  'small': 'of a size that is less than normal or usual'
}
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.words).toMatchObject({
      type: 'dict',
      datatype: 'dict',
      name: 'words',
      keys: [ 1, "'big'", "'small'" ],
      methods: [
        'get',      'setdefault',
        'pop',      'popitem',
        'keys',     'items',
        'values',   'update',
        'clear',    'copy',
        'fromkeys'
      ],
      properties: []
    })
  });

  it('can detect dict variables with dict() syntax', () => {
    const code = `
lang = dict({1: 'Python', 2: 'Example', 'Author': 'Jeff'})
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.lang).toMatchObject({
      type: 'dict',
      datatype: 'dict',
      name: 'lang',
      keys: [ 1, 2, "'Author'" ],
      methods: [
        'get',      'setdefault',
        'pop',      'popitem',
        'keys',     'items',
        'values',   'update',
        'clear',    'copy',
        'fromkeys'
      ],
      properties: []
    })
  });

  it('can detect dict variables with alternate dict() syntax', () => {
    const code = `
person = dict(firstName="Donald", lastName="Bradman", age=93)
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.person).toMatchObject({
      type: 'dict',
      datatype: 'dict',
      name: 'person',
      keys: [ "'firstName'", "'lastName'", "'age'" ],
      methods: [
        'get',      'setdefault',
        'pop',      'popitem',
        'keys',     'items',
        'values',   'update',
        'clear',    'copy',
        'fromkeys'
      ],
      properties: []
    })
  });

  it('can detect additions to dict', () => {
    const code = `
words = {
  1: 'Not sure',
  'big': 'of considerable size or extent',
  'small': 'of a size that is less than normal or usual'
}
words['large'] = 'of considerable or relatively great size, extent, or capacity'
words[2] = 10
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.words).toMatchObject({
      type: 'dict',
      datatype: 'dict',
      name: 'words',
      keys: [ 1, "'big'", "'small'", "'large'", 2 ],
      methods: [
        'get',      'setdefault',
        'pop',      'popitem',
        'keys',     'items',
        'values',   'update',
        'clear',    'copy',
        'fromkeys'
      ],
      properties: []
    })
  });

  it('can detect arguments to methods when in scope', () => {
    const code = `
class Ball:
    def __init__(self, x, y, radius):
        self.x = x
        self.y = y
        self.radius = radius

    def draw(self):
        print(self.x)
        print(self.y)
        print(self.radius)
ball = Ball(10, 20, 5)
`;
    a.setCode(code);
    const level = 1
    const lineNo = 6
    const completions = a.getCompletions(level, lineNo);
    expect(completions.vars.x).toMatchObject({
      type: 'Parameter', datatype: 'unknown', name: 'x'
    })
    expect(completions.vars.y).toMatchObject({
      type: 'Parameter', datatype: 'unknown', name: 'y'
    })
    expect(completions.vars.radius).toMatchObject({
      type: 'Parameter', datatype: 'unknown', name: 'radius'
    })
    expect(completions.vars.self).toMatchObject({
      type: 'Ball',
      methods: [ 'draw' ],
      properties: [ 'x', 'y', 'radius' ]
    })
    expect(completions.classes.Ball).toMatchObject({
      methods: [ 'draw' ],
      properties: [ 'x', 'y', 'radius' ],
      signature: '(x, y, radius)',
      isException: false
    })
  });

  it('can detect arguments to functions when in scope', () => {
    const code = `
def greet(name):
    print('Hello ' + name)

greet('Jeff')
`;
    a.setCode(code);
    const level = 1
    const lineNo = 2
    const completions = a.getCompletions(level, lineNo);
    expect(completions.vars.name).toMatchObject({
      type: 'Parameter', datatype: 'unknown', name: 'name'
    })
  });

  it('can detect functions names after they are declared', () => {
    const code = `
def greet(name):
    print('Hello ' + name)

greet('Jeff')
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.name).toBeUndefined();
    expect(completions.functions.greet).toMatchObject({
      signature: '(name)', doc: 'Function'
    })
  });

  it('can detect variables in while loops', () => {
    const code = `
while True:
    score = 10
    cost = 1.01
    fun = True
    break
print(score)
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.score).toMatchObject({
      type: 'Variable',
      datatype: 'int',
      name: 'score',
      methods: [ 'conjugate', 'bit_length', 'to_bytes', 'from_bytes' ],
      properties: [ 'real', 'imag', 'numerator', 'denominator' ]
    })
    expect(completions.vars.cost).toMatchObject({
      type: 'Variable',
      datatype: 'float',
      name: 'cost',
      methods: [ 'conjugate', 'as_integer_ratio', 'is_integer' ],
      properties: [ 'real', 'imag' ]
    })
    expect(completions.vars.fun).toMatchObject({
      type: 'Variable',
      datatype: 'bool',
      name: 'fun',
      methods: [],
      properties: []
    })
  });

  it('can detect variables in forever loops', () => {
    const code = `
forever:
    score = 100
    averageNum = 53.23
    numbers = [1, 2, 3]
    break
print(score)
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.score).toMatchObject({
      type: 'Variable',
      datatype: 'int',
      name: 'score',
      methods: [ 'conjugate', 'bit_length', 'to_bytes', 'from_bytes' ],
      properties: [ 'real', 'imag', 'numerator', 'denominator' ]
    })
    expect(completions.vars.averageNum).toMatchObject({
      type: 'Variable',
      datatype: 'float',
      name: 'averageNum',
      methods: [ 'conjugate', 'as_integer_ratio', 'is_integer' ],
      properties: [ 'real', 'imag' ]
    })
    expect(completions.vars.numbers).toMatchObject({
      type: 'Variable',
      datatype: 'list',
      name: 'numbers',
      methods: [
        'clear',   'copy',
        'append',  'insert',
        'extend',  'pop',
        'remove',  'sort',
        'index',   'count',
        'reverse'
      ],
      properties: []
    })
  });

  it('can detect variables in if statements', () => {
    const code = `
score = 10
if score > 10:
    print("Winner")
    varA = "Hello"
elif score == 10:
    print("Draw")
    varB = 10
elif score > 8:
    varC = True
else:
    print("Keep trying!")
    varD = 0.34
print(varB)
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.score).toMatchObject({
      type: 'Variable',
      datatype: 'int',
      name: 'score',
      methods: [ 'conjugate', 'bit_length', 'to_bytes', 'from_bytes' ],
      properties: [ 'real', 'imag', 'numerator', 'denominator' ]
    })
    expect(completions.vars.varA).toMatchObject({
      type: 'Variable',
      datatype: 'str',
      name: 'varA',
      methods: [
        'encode',       'replace',    'split',
        'rsplit',       'join',       'capitalize',
        'title',        'center',     'count',
        'expandtabs',   'find',       'partition',
        'index',        'ljust',      'lower',
        'lstrip',       'rfind',      'rindex',
        'rjust',        'rstrip',     'rpartition',
        'splitlines',   'strip',      'swapcase',
        'upper',        'startswith', 'endswith',
        'isascii',      'islower',    'isupper',
        'istitle',      'isspace',    'isdigit',
        'isnumeric',    'isalpha',    'isalnum',
        'isidentifier', 'zfill',      'format'
      ],
      properties: []
    })
    expect(completions.vars.varB).toMatchObject({
      type: 'Variable',
      datatype: 'int',
      name: 'varB',
      methods: [ 'conjugate', 'bit_length', 'to_bytes', 'from_bytes' ],
      properties: [ 'real', 'imag', 'numerator', 'denominator' ]
    })
    expect(completions.vars.varC).toMatchObject({
      type: 'Variable',
      datatype: 'bool',
      name: 'varC',
      methods: [],
      properties: []
    })
    expect(completions.vars.varD).toMatchObject({
      type: 'Variable',
      datatype: 'float',
      name: 'varD',
      methods: [ 'conjugate', 'as_integer_ratio', 'is_integer' ],
      properties: [ 'real', 'imag' ]
    })
  });

  it('can detect variables in for loops with string', () => {
    const code = `
for a in "Hello":
    doubleLast = a + a
    score = 10
print(doubleLast)
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.a).toMatchObject({
      type: 'Variable',
      datatype: 'str',
      name: 'a',
      methods: [
        'encode',       'replace',    'split',
        'rsplit',       'join',       'capitalize',
        'title',        'center',     'count',
        'expandtabs',   'find',       'partition',
        'index',        'ljust',      'lower',
        'lstrip',       'rfind',      'rindex',
        'rjust',        'rstrip',     'rpartition',
        'splitlines',   'strip',      'swapcase',
        'upper',        'startswith', 'endswith',
        'isascii',      'islower',    'isupper',
        'istitle',      'isspace',    'isdigit',
        'isnumeric',    'isalpha',    'isalnum',
        'isidentifier', 'zfill',      'format'
      ],
      properties: []
    })
    expect(completions.vars.doubleLast).toMatchObject({
      type: 'Variable',
      datatype: 'Unknown',
      name: 'doubleLast',
      methods: [],
      properties: []

    })
    expect(completions.vars.score).toMatchObject({
      type: 'Variable',
      datatype: 'int',
      name: 'score',
      methods: [ 'conjugate', 'bit_length', 'to_bytes', 'from_bytes' ],
      properties: [ 'real', 'imag', 'numerator', 'denominator' ]
    })
  });

  it('can detect variables in for loops with list of ints', () => {
    const code = `
for a in [1, 2, 3]:
    doubleLast = a
    score = 10
print(doubleLast)
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.a).toMatchObject({
      type: 'Variable',
      datatype: 'int',
      name: 'a',
      methods: [ 'conjugate', 'bit_length', 'to_bytes', 'from_bytes' ],
      properties: [ 'real', 'imag', 'numerator', 'denominator' ]
    })
    expect(completions.vars.doubleLast).toMatchObject({
      type: 'Variable',
      datatype: 'Unknown',
      name: 'doubleLast',
      methods: [],
      properties: []

    })
    expect(completions.vars.score).toMatchObject({
      type: 'Variable',
      datatype: 'int',
      name: 'score',
      methods: [ 'conjugate', 'bit_length', 'to_bytes', 'from_bytes' ],
      properties: [ 'real', 'imag', 'numerator', 'denominator' ]
    })
  });

  it('can detect variables in for loops with list of strings', () => {
    const code = `
for fruit in ["apple", "banana", "strawberry"]:
    doubleFruit = fruit * 2
    happy = True
print(doubleFruit)
`;
    a.setCode(code);
    const completions = a.getCompletions();
  });

  it('can detect variables in for loops with named list', () => {
      const code = `
fruits = ["apples", "bananas", "strawberries"]
for fruit in fruits:
    happy = True
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.fruits).toMatchObject({
      type: 'Variable',
      datatype: 'list',
      name: 'fruits',
      methods: [
        'clear',   'copy',
        'append',  'insert',
        'extend',  'pop',
        'remove',  'sort',
        'index',   'count',
        'reverse'
      ],
      properties: []
    })
    expect(completions.vars.fruit).toMatchObject({
      type: 'Variable',
      datatype: 'list',
      name: 'fruit',
      methods: [],
      properties: []
    })
    expect(completions.vars.happy).toMatchObject({
      type: 'Variable',
      datatype: 'bool',
      name: 'happy',
      methods: [],
      properties: []
    })
  });

  it('can detect variables in for loops with list of tuples', () => {
    const code = `
for fruit in ("apples", "bananas", "strawberries"):
    happy = True
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.fruit).toMatchObject({
      type: 'Variable',
      datatype: 'tuple element',
      name: 'fruit',
      methods: [],
      properties: []
    })
    expect(completions.vars.happy).toMatchObject({
      type: 'Variable',
      datatype: 'bool',
      name: 'happy',
      methods: [],
      properties: []
    })
  });

  it('can detect variables in for loops with function call', () => {
    const code = `
def returnList():
    return [1, 2, 3]
for fruit in returnList():
    happy = True
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.fruit).toMatchObject({
      type: 'Variable',
      datatype: 'returnList element',
      name: 'fruit',
      methods: [],
      properties: []
    })
    expect(completions.vars.happy).toMatchObject({
      type: 'Variable',
      datatype: 'bool',
      name: 'happy',
      methods: [],
      properties: []
    })
  });
});
