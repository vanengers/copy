# copy
Symfony Console Command to copy folders and files

## Installation

```
composer require vanengers/symfony-console-command-lib
``` 

## Usage

```
php bin/copy --source=C:\toCopyPath --destination=C:\destinationPath --excludes=vendor,node_modules
```

By default Composer files, VCS and Dot files/folders are excluded. You can include them by adding the --ignoreVCS=false, --ignoreDotFiles=false and/or --ignoreComposer=false flags

```
php bin/copy --source=C:\toCopyPath --destination=C:\destinationPath --excludes=vendor,node_modules --ignoreVCS=false --ignoreDotFiles=false --ignoreComposer=false
```

### How it works.
It copies all files and folders per file/folder. It does not use mirroring, because symlinks are followed, but the extra vendor directory does not need to be copied.
Thus resulting in a slower copying process, but making an identical copy of the source directory including the symlinks / vender packages.