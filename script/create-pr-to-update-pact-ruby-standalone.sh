#!/usr/bin/env bash -e

set -x

: "${1?Please supply the pact-ruby-standalone version to upgrade to}"

STANDALONE_VERSION=$1
DASHERISED_VERSION=$(echo "${STANDALONE_VERSION}" | sed 's/\./\-/g')
BRANCH_NAME="chore/upgrade-to-pact-ruby-standalone-${DASHERISED_VERSION}"
VERSION_FILES="src/PhpPact/Standalone/Installer/Service/InstallerLinux.php src/PhpPact/Standalone/Installer/Service/InstallerMac.php src/PhpPact/Standalone/Installer/Service/InstallerWindows.php"

git checkout master
git reset head ${VERSION_FILES}
git checkout ${VERSION_FILES}
git pull origin master

git checkout -b ${BRANCH_NAME}

for file in $VERSION_FILES; do
  cat ${file} | sed "s/.*const VERSION = .*/\    const VERSION = '${STANDALONE_VERSION}';/" > tmp-version-file
  mv tmp-version-file ${file}
done


git add ${VERSION_FILES}
git commit -m "feat(upgrade): update standalone to ${STANDALONE_VERSION}"
git push --set-upstream origin ${BRANCH_NAME}

hub pull-request --browse --message "feat: update standalone to ${STANDALONE_VERSION}"

git checkout master
