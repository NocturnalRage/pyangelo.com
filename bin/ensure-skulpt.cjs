// Builds Skulpt in node_modules/skulpt if its dist files are missing, as they
// are after a fresh npm install or npm run refreshskulpt: they are not in
// Skulpt's git repository, and the Vite build imports them.
// npm run prod and npm run production always rebuild Skulpt instead.
const { existsSync } = require('fs')
const { execSync } = require('child_process')
const path = require('path')

const root = path.resolve(__dirname, '..')
const dist = ['skulpt.min.js', 'skulpt-stdlib.js']
  .map(file => path.join(root, 'node_modules/skulpt/dist', file))

if (dist.every(file => existsSync(file))) {
  process.exit(0)
}
console.log('Skulpt is not built yet; running npm run skulpt')
execSync('npm run skulpt', { cwd: root, stdio: 'inherit' })
