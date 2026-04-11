module.exports = {
  edit: jest.fn(() => ({
    setTheme: jest.fn(),
    session: { setMode: jest.fn() },
    setOptions: jest.fn(),
    setValue: jest.fn(),
    getValue: jest.fn(() => ''),
    on: jest.fn(),
    renderer: { on: jest.fn() }
  }))
}
