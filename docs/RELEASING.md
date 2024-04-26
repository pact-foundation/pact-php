# Pact-PHP Releasing

Pact-PHP packages are published to Packagist

- https://packagist.org/packages/pact-foundation/pact-php

## Release Process

The release process is automated via GitHub Release tags, and Packagist Webhooks.

1. Create a tag from the master branch in GitHub
  - New versions of your package are automatically fetched from tags you create in your VCS repository.
2. Release to Packagist
  - A GitHub webhook will inform packagist that the package has been updated.
3. Create a release from the tag in GitHub
  - Set it to `latest`
  - Click `Generate CHANGELOG` to fill out a changelog.
  - Click `Publish Release`
