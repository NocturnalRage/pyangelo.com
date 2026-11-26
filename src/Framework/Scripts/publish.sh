#!/usr/bin/env bash
# Copies a built release from the git checkout to the live site.
# Usage: publish.sh GITDIR APPHOME
#
# The site keeps serving requests while this runs, so it works in two phases:
#   1. Prepare: copy everything beside the live files and check the new build
#      is complete. Nothing the site uses changes, so if a step fails the old
#      release is still intact.
#   2. Switch: rename the manifest, config/, src/ and views/ into place, one
#      straight after another, so the new build, routes, code and templates go
#      live together. The old copies are kept until all four are in place; if
#      any step fails, the ones already switched are swapped back, so the old
#      release is live again as a whole.
# A file is never rewritten while it is live: new files are copied under a
# temporary name and renamed, and directories are swapped with mv --exchange.
set -eo pipefail

GITDIR="$1"
APPHOME="$2"
if [ ! -d "$GITDIR" ] || [ ! -d "$APPHOME" ]; then
  echo "Usage: publish.sh GITDIR APPHOME" >&2
  exit 1
fi
BUILD="$APPHOME/public/build"
MANIFEST="$BUILD/.vite/manifest.json"

# Copy a directory beside the live one as NAME.new.
stage_dir () {
  rm -rf "$APPHOME/$1.new" "$APPHOME/$1.old"
  cp -r "$GITDIR/$1" "$APPHOME/$1.new"
}

# Put NAME.new in place of NAME and remove the old copy straight away. Used
# for images and samples, which nothing else depends on.
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

# Put NAME.new in place of NAME, keeping the old copy so swap_back can
# restore it. mv --exchange (GNU coreutils 9.5+) swaps the two in one step,
# leaving the old copy at NAME.new. Without it, two renames leave the old
# copy at NAME.old, and NAME missing for a moment between them. Records what
# it did in SWAPPED. Returns non-zero, with NAME unchanged, if it fails.
SWAPPED=()
swap_in () {
  if [ ! -e "$APPHOME/$1" ]; then
    mv "$APPHOME/$1.new" "$APPHOME/$1" || return 1
    SWAPPED+=("$1:fresh")
  elif mv --exchange -T "$APPHOME/$1.new" "$APPHOME/$1" 2>/dev/null; then
    SWAPPED+=("$1:exchange")
  else
    mv "$APPHOME/$1" "$APPHOME/$1.old" || return 1
    if ! mv "$APPHOME/$1.new" "$APPHOME/$1"; then
      mv "$APPHOME/$1.old" "$APPHOME/$1"
      return 1
    fi
    SWAPPED+=("$1:rename")
  fi
}

# Undo every swap_in, newest first, putting the old copies back.
swap_back () {
  local i name how
  for ((i = ${#SWAPPED[@]} - 1; i >= 0; i--)); do
    name="${SWAPPED[i]%%:*}"
    how="${SWAPPED[i]##*:}"
    case "$how" in
      exchange) mv --exchange -T "$APPHOME/$name.new" "$APPHOME/$name" ;;
      rename) mv "$APPHOME/$name" "$APPHOME/$name.new" && mv "$APPHOME/$name.old" "$APPHOME/$name" ;;
      fresh) mv "$APPHOME/$name" "$APPHOME/$name.new" ;;
    esac
  done
}

# After a successful switch, delete the old copies swap_in kept.
remove_old_copies () {
  local entry
  for entry in "${SWAPPED[@]}"; do
    rm -rf "$APPHOME/${entry%%:*}.new" "$APPHOME/${entry%%:*}.old"
  done
}

# Print every file a Vite manifest lists (scripts, CSS and assets such as
# fonts), relative to public/build. Paths that leave public/build are skipped.
# Fails if the manifest cannot be read.
manifest_files () {
  php -r '
    $chunks = json_decode((string) file_get_contents($argv[1]), true);
    if (! is_array($chunks)) {
      fwrite(STDERR, "Cannot read the Vite manifest {$argv[1]}\n");
      exit(1);
    }
    foreach ($chunks as $chunk) {
      if (! is_array($chunk)) continue;
      $files = array_merge([$chunk["file"] ?? null], $chunk["css"] ?? [], $chunk["assets"] ?? []);
      foreach ($files as $file) {
        if (is_string($file) && $file !== "" && $file[0] !== "/" && ! str_contains($file, "..")) {
          echo $file, "\n";
        }
      }
    }' "$1"
}

# --- Prepare ---------------------------------------------------------------

# Vite names every file by a hash of its contents, so a file already on the
# live site under the same name is identical: leave its contents alone, since
# the site may be serving it right now, and only give it the current time so
# it is not deleted as old. Copy new files under a temporary name and rename
# them, so no request can see a half-written file. The live manifest does not
# refer to them until it is switched below.
echo "Adding the new build files to public/build"
if [ ! -d "$GITDIR/public/build/assets" ] || [ ! -f "$GITDIR/public/build/.vite/manifest.json" ]; then
  echo "The build in $GITDIR/public/build is missing its assets or manifest" >&2
  exit 1
fi
mkdir -p "$BUILD/.vite"
(cd "$GITDIR/public/build" && find assets -type f) |
while IFS= read -r file; do
  if [ -f "$BUILD/$file" ]; then
    touch "$BUILD/$file"
  else
    mkdir -p "$(dirname "$BUILD/$file")"
    cp "$GITDIR/public/build/$file" "$BUILD/$file.tmp"
    mv "$BUILD/$file.tmp" "$BUILD/$file"
  fi
done
cp "$GITDIR/public/build/.vite/manifest.json" "$MANIFEST.new"

# Never switch to a manifest that points at files the site does not have.
echo "Checking every file the new manifest lists is in place"
if ! listed=$(manifest_files "$MANIFEST.new"); then
  rm -f "$MANIFEST.new"
  exit 1
fi
missing=$(printf '%s\n' "$listed" | while IFS= read -r file; do
  if [ -n "$file" ] && [ ! -f "$BUILD/$file" ]; then echo "$file"; fi
done)
if [ -n "$missing" ]; then
  echo "The new build is missing files its manifest lists:" >&2
  echo "$missing" >&2
  rm -f "$MANIFEST.new"
  exit 1
fi

echo "Copying the new release beside the live one"
# config/ holds routes and services, so it goes live with src/. The live
# config/ also holds files that are not in git, such as the CloudFront key,
# so the new copy starts from the live one and the release's files are copied
# over it.
rm -rf "$APPHOME/config.new" "$APPHOME/config.old"
if [ -d "$APPHOME/config" ]; then
  cp -a "$APPHOME/config" "$APPHOME/config.new"
else
  mkdir "$APPHOME/config.new"
fi
cp -R "$GITDIR/config/." "$APPHOME/config.new/"
stage_dir src
stage_dir views
stage_dir public/images
stage_dir public/samples
cp "$GITDIR/public/index.php" "$APPHOME/public/index.php.new"

# The files the live manifest lists are retired by this release, but pages
# opened moments ago still need them. Give them the current time, so the week
# before they are deleted starts now, not when an earlier release copied them.
# An unreadable live manifest only means nothing is re-dated.
echo "Retiring the files the live build uses"
if [ -f "$MANIFEST" ]; then
  (manifest_files "$MANIFEST" 2>/dev/null || true) |
  while IFS= read -r file; do
    if [ -f "$BUILD/$file" ]; then
      touch "$BUILD/$file"
    fi
  done
fi

# --- Switch ----------------------------------------------------------------

echo "Switching to the new release"
rm -f "$MANIFEST.previous"
if [ -f "$MANIFEST" ]; then
  cp "$MANIFEST" "$MANIFEST.previous"
fi
switched=true
mv "$MANIFEST.new" "$MANIFEST" || switched=false
for name in config src views; do
  if [ "$switched" = true ] && ! swap_in "$name"; then
    switched=false
  fi
done
if [ "$switched" != true ]; then
  swap_back
  if [ -f "$MANIFEST.previous" ]; then
    mv "$MANIFEST.previous" "$MANIFEST"
  fi
  echo "Could not switch to the new release; the previous manifest, config/, src/ and views/ are back in place" >&2
  exit 1
fi
remove_old_copies
rm -f "$MANIFEST.previous"
mv "$APPHOME/public/index.php.new" "$APPHOME/public/index.php"
swap_dir public/images
swap_dir public/samples

# Files in this build and files just retired both have the current time, so
# only files retired over a week ago are removed.
echo "Removing build files retired over a week ago"
find "$BUILD/assets" -type f -mtime +7 -delete
find "$BUILD/assets" -mindepth 1 -type d -empty -delete
