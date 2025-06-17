const Sk = require('skulpt');
import { Autocompleter } from './Autocompleter';

describe('Autocompleter: Built-in Type Methods', () => {
  let a;

  beforeEach(() => {
    a = new Autocompleter(Sk);
  });

  it('infers str methods correctly', () => {
    a.setCode('s = "hello"');
    const completions = a.getCompletions();
    expect(completions.vars.s.methods).toEqual(expect.arrayContaining([
      'capitalize', 'center', 'count', 'encode', 'endswith',
      'expandtabs', 'find', 'format', 'index', 'isalnum', 'isalpha',
      'isascii', 'isdigit', 'isidentifier', 'islower',
      'isnumeric', 'isspace', 'istitle', 'isupper',
      'join', 'ljust', 'lower', 'lstrip', 'partition', 'replace',
      'rfind', 'rindex', 'rjust', 'rpartition', 'rsplit', 'rstrip',
      'split', 'splitlines', 'startswith', 'strip', 'swapcase',
      'title', 'upper', 'zfill'
    ]));
  });

  it('infers list methods correctly', () => {
    a.setCode('x = [1, 2, 3]');
    const completions = a.getCompletions();
    expect(completions.vars.x.methods).toEqual(expect.arrayContaining([
      'append', 'clear', 'copy', 'count', 'extend', 'index',
      'insert', 'pop', 'remove', 'reverse', 'sort'
    ]));
  });

  it('infers dict methods correctly', () => {
    a.setCode('d = {"a": 1}');
    const completions = a.getCompletions();
    expect(completions.vars.d.methods).toEqual(expect.arrayContaining([
      'clear', 'copy', 'fromkeys', 'get', 'items', 'keys',
      'pop', 'popitem', 'setdefault', 'update', 'values'
    ]));
  });

  it('infers dict methods correctly with dict call', () => {
    a.setCode('d = dict(a = 1)');
    const completions = a.getCompletions();
    expect(completions.vars.d.methods).toEqual(expect.arrayContaining([
      'clear', 'copy', 'fromkeys', 'get', 'items', 'keys',
      'pop', 'popitem', 'setdefault', 'update', 'values'
    ]));
  });

  it('infers float methods correctly', () => {
    a.setCode('f = 3.14');
    const completions = a.getCompletions();
    expect(completions.vars.f.methods).toEqual(expect.arrayContaining([
      'is_integer'
    ]));
  });

  it('infers int methods (likely empty or minimal)', () => {
    a.setCode('n = 42');
    const completions = a.getCompletions();
    expect(Array.isArray(completions.vars.n.methods)).toBe(true);
  });

  it('infers bool methods (likely empty)', () => {
    a.setCode('flag = True');
    const completions = a.getCompletions();
    expect(Array.isArray(completions.vars.flag.methods)).toBe(true);
  });

  it('infers tuple methods correctly', () => {
    a.setCode('t = (1, 2, 3)');
    const completions = a.getCompletions();
    expect(completions.vars.t.methods).toEqual(expect.arrayContaining([
      'count', 'index'
    ]));
  });

  it('infers set methods correctly', () => {
    a.setCode('s = {1, 2, 3}');
    const completions = a.getCompletions();
    expect(completions.vars.s.methods).toEqual(expect.arrayContaining([
      'add', 'clear', 'copy', 'difference_update', 'difference', 'discard',
      'intersection_update', 'intersection', 'isdisjoint', 'issubset',
      'issuperset', 'pop', 'remove', 'symmetric_difference',
      'symmetric_difference_update', 'union', 'update'
    ]));
  });

  it('infers set methods correctly with set call', () => {
    a.setCode('s = set([1, 2, 3])');
    const completions = a.getCompletions();
    expect(completions.vars.s.methods).toEqual(expect.arrayContaining([
      'add', 'clear', 'copy', 'difference_update', 'difference', 'discard',
      'intersection_update', 'intersection', 'isdisjoint', 'issubset',
      'issuperset', 'pop', 'remove', 'symmetric_difference',
      'symmetric_difference_update', 'union', 'update'
    ]));
  });
});
