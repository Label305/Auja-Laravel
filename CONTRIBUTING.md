# Reporting issues

Found a bug in the library? Don't hesitate to open an issue! But first:

* Check if it hasn't been reported yet.
* Verify the bug still exists in the `dev` branch.

When describing an issue, be precise:

* Include steps to reproduce (including sample code if necessary);
* What happened? What do you think should have happened?
* Which relevant versions are you using?

# Contributing code

## Please do!

We're happy to view and accept pull requests. However, it is important to follow these guidelines if you want to contribute.

## General steps

* [Fork](https://github.com/Label305/Auja-Laravel/fork) the repository.
* Create a local clone of the repository.
* Create a local branch, **based on the `dev` branch** (see the *Rules* section)
* Commit your code, and push the changes to your own repository.
* Create a pull request, specifying that you want to merge into the `dev` branch (or any child branch of it)

## Rules

* Branch names should start with either `feature_` or `bugfix_`. If there is an open issue, include its number, like `bugfix_123`.
* **Do not** include in your commit message anything related to automatic issue closing, such as `Fixes issue 123`. We'll do that when merging your pull request.
* **Do not** put any `@author` comments in your code. Git keeps track of all your changes and `@author` does more harm than good.
* **Do not** issue a pull request into the `master` branch.
* Try to keep the diff as small as possible. For example, be aware of auto formatting.
* All files should have the Apache 2 License header.
