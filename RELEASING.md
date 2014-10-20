# Releasing

 1. Update the `CHANGELOG.md` file with relevant info and date;
 2. Update version numbers:
  - `README.md`
 3. Commit: `git commit -am "Prepare version X.Y.Z."`;
 4. Merge into `master`: `git checkout master && git merge dev`;
 5. Tag: `git tag -a X.Y.Z -m "Version X.Y.Z"`;
 6. Push: `git push && git push --tags`;
 7. Checkout `dev` for further development: `git checkout dev`;
 8. Update release information on https://github.com/Label305/Auja-Laravel/releases;
 9. Grab a coffee.