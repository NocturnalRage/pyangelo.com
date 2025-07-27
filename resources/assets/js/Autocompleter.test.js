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
    expect(completions.vars.DEGREES).toMatchObject({
      type: 'Builtin Variable',
      datatype: 'int',
      name: 'DEGREES',
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
      signature: '(width, height, yAxisMode)',
      doc: 'Sets the size of the canvas that all drawings are written to. The first parameter specifies the width in pixels and the second the height. The thrid parameter specifies the direction of the y axis. The constant CARTESIAN can be used to specify the y axis acts like a regular cartesian plane in maths, and JAVASCRIPT can be used to specify a traditional javascript y-axis that moves down the screen. The default value for yAxisMode is CARTESIAN.'
    })
    expect(completions.classes.Image).toMatchObject({
      methods: [
        'setOpacity', 'setRotation', 'setScale',
        'setFrameSize', 'setFlipX', 'setFlipY', 'setPivot',
        'draw', 'drawRegion', 'drawSubImage', 'drawFrame', 'dispose'
      ], properties: [], isException: false
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
        'lstrip',       'removeprefix', 'removesuffix', 'rfind',      'rindex',
        'rjust',        'rstrip',     'rpartition',
        'splitlines',   'strip',      'swapcase',
        'upper',        'startswith', 'endswith',
        'isascii',      'islower',    'isupper',
        'istitle',      'isspace',    'isdecimal', 'isdigit',
        'isnumeric',    'isalpha',    'isalnum',
        'isidentifier', 'isprintable', 'zfill',      'format'
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
        'lstrip',       'removeprefix', 'removesuffix', 'rfind',      'rindex',
        'rjust',        'rstrip',     'rpartition',
        'splitlines',   'strip',      'swapcase',
        'upper',        'startswith', 'endswith',
        'isascii',      'islower',    'isupper',
        'istitle',      'isspace',    'isdecimal', 'isdigit',
        'isnumeric',    'isalpha',    'isalnum',
        'isidentifier', 'isprintable', 'zfill',      'format'
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
        'lstrip',       'removeprefix', 'removesuffix', 'rfind',      'rindex',
        'rjust',        'rstrip',     'rpartition',
        'splitlines',   'strip',      'swapcase',
        'upper',        'startswith', 'endswith',
        'isascii',      'islower',    'isupper',
        'istitle',      'isspace',    'isdecimal', 'isdigit',
        'isnumeric',    'isalpha',    'isalnum',
        'isidentifier', 'isprintable', 'zfill',      'format'
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
        'lstrip',       'removeprefix', 'removesuffix', 'rfind',      'rindex',
        'rjust',        'rstrip',     'rpartition',
        'splitlines',   'strip',      'swapcase',
        'upper',        'startswith', 'endswith',
        'isascii',      'islower',    'isupper',
        'istitle',      'isspace',    'isdecimal', 'isdigit',
        'isnumeric',    'isalpha',    'isalnum',
        'isidentifier', 'isprintable', 'zfill',      'format'
      ],
      properties: []
    })
    expect(completions.vars.doubleLast).toMatchObject({
      type: 'Variable',
      datatype: 'str',
      name: 'doubleLast',
      methods: [
        'encode',       'replace',    'split',
        'rsplit',       'join',       'capitalize',
        'title',        'center',     'count',
        'expandtabs',   'find',       'partition',
        'index',        'ljust',      'lower',
        'lstrip',       'removeprefix', 'removesuffix', 'rfind',      'rindex',
        'rjust',        'rstrip',     'rpartition',
        'splitlines',   'strip',      'swapcase',
        'upper',        'startswith', 'endswith',
        'isascii',      'islower',    'isupper',
        'istitle',      'isspace',    'isdecimal', 'isdigit',
        'isnumeric',    'isalpha',    'isalnum',
        'isidentifier', 'isprintable', 'zfill',      'format'
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
      datatype: 'int',
      name: 'doubleLast',
      methods: [ 'conjugate', 'bit_length', 'to_bytes', 'from_bytes' ],
      properties: [ 'real', 'imag', 'numerator', 'denominator' ]
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
      datatype: 'str',
      name: 'fruit',
      methods: [
        'encode',       'replace',    'split',
        'rsplit',       'join',       'capitalize',
        'title',        'center',     'count',
        'expandtabs',   'find',       'partition',
        'index',        'ljust',      'lower',
        'lstrip',       'removeprefix', 'removesuffix', 'rfind',      'rindex',
        'rjust',        'rstrip',     'rpartition',
        'splitlines',   'strip',      'swapcase',
        'upper',        'startswith', 'endswith',
        'isascii',      'islower',    'isupper',
        'istitle',      'isspace',    'isdecimal', 'isdigit',
        'isnumeric',    'isalpha',    'isalnum',
        'isidentifier', 'isprintable', 'zfill',      'format'
      ],
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
  it('can detect tuple variables', () => {
    const code = `
t = (1, 2, 3)
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.t).toMatchObject({
      type: 'Variable',
      datatype: 'tuple',
      name: 't',
      properties: []
    });
    expect(completions.vars.t.methods).toEqual(
      expect.arrayContaining(['count', 'index'])
    );
  });

  it('can detect variables from subscript of homogeneous list', () => {
    const code = `
scores = [3, 5, 6]
first = scores[0]
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.first).toMatchObject({
      type: 'Variable',
      datatype: 'int',
      name: 'first',
      properties: ['real', 'imag', 'numerator', 'denominator']
    });
    expect(completions.vars.first.methods).toEqual(
      expect.arrayContaining([
        'conjugate', 'bit_length', 'to_bytes', 'from_bytes'
      ])
    );
  });

  it('can detect ternary expressions with matching types', () => {
    const code = `
x = 1 if True else 3
y = "a" if False else "b"
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.x.datatype).toBe('int');
    expect(completions.vars.y.datatype).toBe('str');
  });

  it('can detect ternary expressions with mismatched types', () => {
    const code = `
z = 1 if True else "b"
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.z.datatype).toBe('Unknown');
  });

  it('can detect alias inference for basic variables', () => {
    const code = `
s = "hello"
x = s
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.x.datatype).toBe('str');
  });

  it('can detect NoneType variables', () => {
    const code = `
a = None
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.a).toMatchObject({
      type: 'Variable',
      datatype: 'NoneType',
      name: 'a',
      methods: [],
      properties: []
    });
  });

  it('can detect call to user-defined function returning int', () => {
    const code = `
def get_number():
    return 42
num = get_number()
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.num.datatype).toBe('int');
  });

  it('can detect call to user-defined function returning dict', () => {
    const code = `
def get_map():
    return {"a":1}
m = get_map()
`;
    a.setCode(code);
    const completions = a.getCompletions();
    // it should come back as a dict with no known keys
    expect(completions.vars.m).toMatchObject({
      type: 'dict',
      datatype: 'dict',
      name: 'm',
      keys: []
    });
    expect(completions.vars.m.methods).toEqual(
      expect.arrayContaining([
        'get','setdefault','pop','popitem',
        'keys','items','values','update',
        'clear','copy','fromkeys'
      ])
    );
  });

  it('can detect set variables from set call', () => {
    const code = `
a = set([1,2,3])
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.a).toMatchObject({
      type: 'set',
      datatype: 'set',
      name: 'a'
    });
    expect(completions.vars.a.methods).toEqual(
      expect.arrayContaining(['add','clear','copy'])
    );
    // should not have dict-style keys
    expect(completions.vars.a).not.toHaveProperty('keys');
  });

  it('can detect unknown call assigns Unknown datatype', () => {
    const code = `
x = someUndefinedFunc()
`;
    a.setCode(code);
    const completions = a.getCompletions();
    expect(completions.vars.x.datatype).toBe('Unknown');
  });

  it('picks up attributes from tuple-unpack in __init__', () => {
    const code = `
class Foo:
    def __init__(self, a, b):
        self.a, self.b = a, b
`;
    a.setCode(code);
    const c = a.getCompletions();
    expect(c.classes.Foo.properties).toEqual(expect.arrayContaining(['a','b']));
  });

  it('picks up attributes from chained assignment', () => {
    const code = `
class Counter:
    def __init__(self):
        self.x = self.y = 0
`;
    a.setCode(code);
    const c = a.getCompletions();
    expect(c.classes.Counter.properties).toEqual(expect.arrayContaining(['x','y']));
  });

  it('picks up attributes from augmented assignment', () => {
    const code = `
class Counter:
    def __init__(self, count):
        self.count += count
`;
    a.setCode(code);
    const c = a.getCompletions();
    expect(c.classes.Counter.properties).toEqual(expect.arrayContaining(['count']));
  });

  it('detects dynamic attributes via setattr', () => {
    const code = `
class Foo:
    def __init__(self):
        setattr(self, 'dyn', 123)
`;
    a.setCode(code);
    const c = a.getCompletions();
    expect(c.classes.Foo.properties).toEqual(expect.arrayContaining(['dyn']));
  });

  it('picks up imported classes and lets you complete their instances', () => {
    const code = `
from sprite import RectangleSprite
r = RectangleSprite(0,0,10,20)
`;
    a.setCode(code);
    const obj = a.getCompletions().vars.r;
    expect(obj.methods).toContain('draw');
    expect(obj.properties).toEqual(expect.arrayContaining(['width','height']));
  });

  it('allows subclass to shadow base-class property', () => {
    const code = `
class A:
    @property
    def x(self): return 1
class B(A):
    @property
    def x(self): return 2
`;
    a.setCode(code);
    expect(a.getCompletions().classes.B.properties).toContain('x');
    // and ensure it's not duplicated
    expect(a.getCompletions().classes.B.properties.filter(p => p==='x'))
      .toHaveLength(1);
  });
});
