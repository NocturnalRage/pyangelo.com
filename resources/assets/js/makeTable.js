export function populateTable (tableBody, globals, locals) {
  while (tableBody.firstChild) {
    tableBody.removeChild(tableBody.firstChild)
  }
  for (const key in globals) {
    const row = tableBody.insertRow()
    row.insertCell().appendChild(document.createTextNode(globals[key].scope))
    row.insertCell().appendChild(document.createTextNode(key))
    row.insertCell().appendChild(document.createTextNode(globals[key].value))
    row.insertCell().appendChild(document.createTextNode(globals[key].type))
  }
  for (const key in locals) {
    const row = tableBody.insertRow()
    row.insertCell().appendChild(document.createTextNode(locals[key].scope))
    row.insertCell().appendChild(document.createTextNode(key))
    row.insertCell().appendChild(document.createTextNode(locals[key].value))
    row.insertCell().appendChild(document.createTextNode(locals[key].type))
  }
}
