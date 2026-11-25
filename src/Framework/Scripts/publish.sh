#!/usr/bin/env bash
# Copies a built release from the git checkout to the live site.
# Usage: publish.sh GITDIR APPHOME
#
# The site keeps serving requests while this runs, so every step leaves a
# complete old or new copy in place, never a missing or half-copied one:
# directories are copied beside the live ones and renamed into place, and the
# Vite build is added beside the previous one, with its manifest switched last.
set -e

GITDIR="$1"
APPHOME="$2"
if [ ! -d "$GITDIR" ] || [ ! -d "$APPHOME" ]; then
  echo "Usage: publish.sh GITDIR APPHOME" >&2
  exit 1
fi

# Copy a directory beside the live one as NAME.new.
stage_dir () {
  rm -rf "$APPHOME/$1.new" "$APPHOME/$1.old"
  cp -r "$GITDIR/$1" "$APPHOME/$1.new"
}

# Put NAME.new in place of NAME. mv --exchange (GNU coreutils 9.5+) swaps the
# two in one step, so NAME is never missing. Without it, fall back to two
# renames, which leave NAME missing for a moment between them.
swap_dir () {
  if [ ! -e "$APPHOME/$1" ]; then
    mv "$APPHOME/$1.new" "$APPHOME/$1"
  elif mv --exchange -T "$APPHOME/$1.new" "$APPHOME/$1" 2>/dev/null; then
    rm -rf "$APPHOME/$1.new"
  else
    mv "$APPHOME/$1" "$APPHOME/$1.old"
    mv "$APPHOME/$1.new" "$APPHOME/$1"
    rm -rf "$APPHOME/$1.old"
  fi
}

echo "Copying config directory"
cp -R "$GITDIR/config" "$APPHOME/"

# Vite names files by their content, so new files never overwrite files the
# live manifest points to. Pages already open in a browser keep loading the
# files they asked for. The manifest is renamed into place last.
echo "Adding the new build to public/build"
mkdir -p "$APPHOME/public/build/.vite"
cp -r "$GITDIR/public/build/assets" "$APPHOME/public/build/"
cp "$GITDIR/public/build/.vite/manifest.json" "$APPHOME/public/build/.vite/manifest.json.new"
mv "$APPHOME/public/build/.vite/manifest.json.new" "$APPHOME/public/build/.vite/manifest.json"

# cp gives every file it copies the current time, so only files that no build
# has included for a week are removed.
echo "Removing build files older than a week"
find "$APPHOME/public/build/assets" -type f -mtime +7 -delete
find "$APPHOME/public/build/assets" -mindepth 1 -type d -empty -delete

echo "Replacing public images and samples directories"
stage_dir public/images
swap_dir public/images
stage_dir public/samples
swap_dir public/samples

echo "Upgrade front controller script"
cp "$GITDIR/public/index.php" "$APPHOME/public/index.php.new"
mv "$APPHOME/public/index.php.new" "$APPHOME/public/index.php"

# Copy both before swapping either, so the new src/ and views/ go live
# within moments of each other.
echo "Replacing src and views directories"
stage_dir src
stage_dir views
swap_dir src
swap_dir views
