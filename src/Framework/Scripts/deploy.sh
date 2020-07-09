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
npm install
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

echo "Replacing public brython directory"
rm -r $APPHOME/public/brython
cp -r $GITDIR/public/brython $APPHOME/public/

echo "Upgrade front controller script"
cp $GITDIR/public/index.php $APPHOME/public/

echo "Replacing src directory"
rm -r $APPHOME/src
cp -r $GITDIR/src $APPHOME/

echo "Replacing views directory"
rm -r $APPHOME/views
cp -r $GITDIR/views $APPHOME/
