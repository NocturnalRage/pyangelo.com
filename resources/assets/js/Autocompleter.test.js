const Sk = require('skulpt')
import { Autocompleter } from './Autocompleter'

it('can create Autocompleter', () => {
  const a = new Autocompleter(Sk); expect(a.Sk).toBe(Sk);
});

it('can set code', () => {
  const a = new Autocompleter(Sk);
  const code = 'print("Hello")';
  a.setCode(code);
  expect(a.code).toBe(code);
});

it('can detect basic variables', () => {
  const a = new Autocompleter(Sk);
  const code = `
fun = "yes"
score = 0
cool = True
`;
  a.setCode(code);
  const completions = a.getCompletions();
  const expectedCompletions = {
    vars: {
      fun: { type: 'Variable', datatype: 'String', name: 'fun' },
      score: { type: 'Variable', datatype: 'Number', name: 'score' },
      cool: { type: 'Variable', datatype: 'Constant', name: 'cool' },
    },
    classes: {},
    functions: {}
  };
  expect(completions).toEqual(expectedCompletions);
});

it('can detect in scope variables', () => {
  const a = new Autocompleter(Sk);
  const code = `
fun = "yes"
score = 0
cool = True
`;
  a.setCode(code);
  const level = 1
  const lineNo = 2
  const completions = a.getCompletions(level, lineNo);
  const expectedCompletions = {
    vars: {
      fun: { type: 'Variable', datatype: 'String', name: 'fun' }
    },
    classes: {},
    functions: {}
  };
  expect(completions).toEqual(expectedCompletions);
});

it('can detect classes', () => {
  const a = new Autocompleter(Sk);
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
  const expectedCompletions = {
    vars: {},
    classes: {
      "Ball": {
        "methods": ['draw'],
        "properties": ['x', 'y', 'radius']
      }
    },
    functions: {}
  };
  expect(completions).toEqual(expectedCompletions);
});

it('can detect inherited classes', () => {
  const a = new Autocompleter(Sk);
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
  const expectedCompletions = {
    vars: {},
    classes: {
      "Ball": {
        "methods": ['draw'],
        "properties": ['x', 'y', 'radius']
      },
      "ColourBall": {
        "methods": ['draw', 'setColour'],
        "properties": ['x', 'y', 'radius', 'colour']
      }
    },
    functions: {}
  };
  expect(completions).toEqual(expectedCompletions);
});

it('can detect objects', () => {
  const a = new Autocompleter(Sk);
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
  const expectedCompletions = {
    vars: {
      'ball': {
        type: 'Ball',
        datatype: 'Object',
        methods: ['draw'],
        properties: ['x', 'y', 'radius']
      }
    },
    classes: {
      "Ball": {
        "methods": ['draw'],
        "properties": ['x', 'y', 'radius']
      }
    },
    functions: {}
  };
  expect(completions).toEqual(expectedCompletions);
});

it('can detect dynamic properties added to objects', () => {
  const a = new Autocompleter(Sk);
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
  const expectedCompletions = {
    vars: {
      'ball': {
        type: 'Ball',
        datatype: 'Object',
        methods: ['draw'],
        properties: ['x', 'y', 'radius', 'owner']
      }
    },
    classes: {
      "Ball": {
        "methods": ['draw'],
        "properties": ['x', 'y', 'radius']
      }
    },
    functions: {}
  };
  expect(completions).toEqual(expectedCompletions);
});

it('can detect list variables', () => {
  const a = new Autocompleter(Sk);
  const code = `
scores = [3, 5, 6]
`;
  a.setCode(code);
  const completions = a.getCompletions();
  const expectedCompletions = {
    vars: {
      scores: {
        type: 'Variable',
        datatype: 'List',
        name: 'scores',
        methods: [
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
          'sort',
        ],
        properties: []
      }
    },
    classes: {},
    functions: {}

  };
  expect(completions).toEqual(expectedCompletions);
});

it('can detect dict variables', () => {
  const a = new Autocompleter(Sk);
  const code = `
words = {
  1: 'Not sure',
  'big': 'of considerable size or extent',
  'small': 'of a size that is less than normal or usual'
}
`;
  a.setCode(code);
  const completions = a.getCompletions();
  const expectedCompletions = {
    vars: {
      words: {
        type: 'Dict', datatype: 'Dict', name: 'words',
        keys: [
          1, "'big'", "'small'"
        ],
        methods: [
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
        ],
        properties: []
      }
    },
    classes: {},
    functions: {}
  };
  expect(completions).toEqual(expectedCompletions);
});

it('can detect dict variables with dict() syntax', () => {
  const a = new Autocompleter(Sk);
  const code = `
lang = dict({1: 'Python', 2: 'Example', 'Author': 'Jeff'})
`;
  a.setCode(code);
  const completions = a.getCompletions();
  const expectedCompletions = {
    vars: {
      lang: {
        type: 'Dict', datatype: 'Dict', name: 'lang',
        keys: [
          1, 2, "'Author'"
        ],
        methods: [
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
        ],
        properties: []
      }
    },
    classes: {},
    functions: {}
  };
  expect(completions).toEqual(expectedCompletions);
});

it('can detect dict variables with alternate dict() syntax', () => {
  const a = new Autocompleter(Sk);
  const code = `
person = dict(firstName="Donald", lastName="Bradman", age=93)
`;
  a.setCode(code);
  const completions = a.getCompletions();
  const expectedCompletions = {
    vars: {
      person: {
        type: 'Dict', datatype: 'Dict', name: 'person',
        keys: [
          "'firstName'", "'lastName'", "'age'"
        ],
        methods: [
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
        ],
        properties: []
      }
    },
    classes: {},
    functions: {}
  };
  expect(completions).toEqual(expectedCompletions);
});

it('can detect additions to dict', () => {
  const a = new Autocompleter(Sk);
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
  const expectedCompletions = {
    vars: {
      words: {
        type: 'Dict', datatype: 'Dict', name: 'words',
        keys: [
          1, "'big'", "'small'", "'large'", 2
        ],
        methods: [
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
        ],
        properties: []
      }
    },
    classes: {},
    functions: {}
  };
  expect(completions).toEqual(expectedCompletions);
});

it('can detect arguments to methods when in scope', () => {
  const a = new Autocompleter(Sk);
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
  const expectedCompletions = {
    vars: {
      'x': {
        'type': 'Variable',
        'datatype': 'Parameter',
        'name': 'x'
      },
      'y': {
        'type': 'Variable',
        'datatype': 'Parameter',
        'name': 'y'
      },
      'radius': {
        'type': 'Variable',
        'datatype': 'Parameter',
        'name': 'radius'
      },
      'self': {
        'type': 'Ball',
        'methods': ['draw'],
        'properties': ['x', 'y', 'radius']
      },
    },
    classes: {
      "Ball": {
        "methods": ['draw'],
        "properties": ['x', 'y', 'radius']
      }
    },
    functions: {}
  };
  expect(completions).toEqual(expectedCompletions);
});

it('can detect arguments to functions when in scope', () => {
  const a = new Autocompleter(Sk);
  const code = `
def greet(name):
    print('Hello ' + name)

greet('Jeff')
`;
  a.setCode(code);
  const level = 1
  const lineNo = 2
  const completions = a.getCompletions(level, lineNo);
  const expectedCompletions = {
    vars: {
      'name': {
        'type': 'Variable',
        'datatype': 'Parameter',
        'name': 'name'
      }
    },
    classes: {},
    functions: {}
  };
  expect(completions).toEqual(expectedCompletions);
});

it('can detect functions names after they are declared', () => {
  const a = new Autocompleter(Sk);
  const code = `
def greet(name):
    print('Hello ' + name)

greet('Jeff')
`;
  a.setCode(code);
  const completions = a.getCompletions();
  const expectedCompletions = {
    vars: {},
    classes: {},
    functions: {
      "greet": "(name)"
    }
  };
  expect(completions).toEqual(expectedCompletions);
});

it('can detect variables in while loops', () => {
  const a = new Autocompleter(Sk);
  const code = `
while True:
    score = 10
    break
print(score)
`;
  a.setCode(code);
  const completions = a.getCompletions();
  const expectedCompletions = {
    vars: {
      score: { type: 'Variable', datatype: 'Number', name: 'score' }
    },
    classes: {},
    functions: {}
  };
  expect(completions).toEqual(expectedCompletions);
});

it('can detect variables in forever loops', () => {
  const a = new Autocompleter(Sk);
  const code = `
forever:
    score = 100
    break
print(score)
`;
  a.setCode(code);
  const completions = a.getCompletions();
  const expectedCompletions = {
    vars: {
      score: { type: 'Variable', datatype: 'Number', name: 'score' }
    },
    classes: {},
    functions: {}
  };
  expect(completions).toEqual(expectedCompletions);
});

it('can detect variables in if statements', () => {
  const a = new Autocompleter(Sk);
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
    varD = 0
print(varB)
`;
  a.setCode(code);
  const completions = a.getCompletions();
  const expectedCompletions = {
    vars: {
      score: { type: 'Variable', datatype: 'Number', name: 'score' },
      varA: { type: 'Variable', datatype: 'String', name: 'varA' },
      varB: { type: 'Variable', datatype: 'Number', name: 'varB' },
      varC: { type: 'Variable', datatype: 'Constant', name: 'varC' },
      varD: { type: 'Variable', datatype: 'Number', name: 'varD' }
    },
    classes: {},
    functions: {}
  };
  expect(completions).toEqual(expectedCompletions);
});

it('can detect variables in for loops', () => {
  const a = new Autocompleter(Sk);
  const code = `
for a in "Hello":
    doubleLast = a + a
    score = 10
print(doubleLast)
`;
  a.setCode(code);
  const completions = a.getCompletions();
  const expectedCompletions = {
    vars: {
      a: { type: 'Variable', datatype: 'String', name: 'a' },
      doubleLast: { type: 'Variable', datatype: 'Unknown', name: 'doubleLast' },
      score: { type: 'Variable', datatype: 'Number', name: 'score' }
    },
    classes: {},
    functions: {}
  };
  expect(completions).toEqual(expectedCompletions);
});

it('can detect methods for list variables', () => {
  const a = new Autocompleter(Sk);
  const code = `
words = ['hello', 'goodbye']
`;
  a.setCode(code);
  const completions = a.getCompletions();
  const expectedCompletions = {
    vars: {
      words: {
        type: 'Variable',
        datatype: 'List',
        name: 'words',
        methods: [
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
          'sort',
        ],
        properties: []
      }
    },
    classes: {},
    functions: {}
  };
  expect(completions).toEqual(expectedCompletions);
});
