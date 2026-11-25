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

echo "Copying config directory"
cp -R $GITDIR/config $APPHOME/

echo "Replacing public fonts directory"
rm -r $APPHOME/public/fonts
cp -r $GITDIR/public/fonts $APPHOME/public/

echo "Replacing mix-manifest.json file"
cp -r $GITDIR/public/mix-manifest.json $APPHOME/public/

echo "Replacing public css directory"
rm -r $APPHOME/public/css
cp -r $GITDIR/public/css $APPHOME/public/

echo "Replacing public js directory"
rm -r $APPHOME/public/js
cp -r $GITDIR/public/js $APPHOME/public/

echo "Replacing public images directory"
rm -r $APPHOME/public/images
cp -r $GITDIR/public/images $APPHOME/public/

echo "Replacing public samples directory"
rm -r $APPHOME/public/samples
cp -r $GITDIR/public/samples $APPHOME/public/

echo "Upgrade front controller script"
cp $GITDIR/public/index.php $APPHOME/public/

echo "Replacing src directory"
rm -r $APPHOME/src
cp -r $GITDIR/src $APPHOME/

echo "Replacing views directory"
rm -r $APPHOME/views
cp -r $GITDIR/views $APPHOME/
