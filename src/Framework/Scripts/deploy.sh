# Stop at the first failure so a failed build is never copied to the live site.
set -e

GITDIR="/home/jeff/pyangelo.com"
APPHOME="/srv/http/pyangelo.com"

echo "Copying composer files"
cp $GITDIR/composer.json $APPHOME/
cp $GITDIR/composer.lock $APPHOME/
chmod 640 $APPHOME/composer.json
chmod 640 $APPHOME/composer.lock

cd $APPHOME
composer install --no-dev

echo "Running Webpack"
cd $GITDIR
# Remove skulpt so npm installs the commit pinned in package-lock.json.
# The server has npm's allow-git turned off, which stops npm fetching git
# dependencies; root allows only the ones listed in package.json (skulpt).
rm -rf "$GITDIR/node_modules/skulpt"
npm install --allow-git=root
npm run production

# Copy the release to the live site without a moment where files are missing.
bash "$GITDIR/src/Framework/Scripts/publish.sh" "$GITDIR" "$APPHOME"
