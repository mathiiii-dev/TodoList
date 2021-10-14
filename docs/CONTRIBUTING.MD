# How to contribute to TodoList
This guideline is destined for the next developer of the application.

If you want to contribute to the TodoList app, please act in accordance with those guidelines.

## Installation

First of all, follow the [README](README.md) to install the project on your local machine.

## Workflow

### Issue:

* If you find a bug or have a question about the project:

  * Make sure your question hasn't been asked before. 
  * If not, open an issue. Be as clear as possible in the title and the description of the issue. 
  * Add code sample, reproducing guidelines or any info that can help understand your problem.
  * Use tags to class your issue (you can use the tag [bug](https://github.com/mathiiii-dev/TodoList/labels/bug))

### Branch: 

When you are developing something please start from the branch [dev](https://github.com/mathiiii-dev/TodoList/tree/dev). 
Then create your own branch and please, name it after the linked issue and use - between each words e.g. : ```my-first-issue```

### Commit: 

This project use the [conventional commits](https://www.conventionalcommits.org/en/v1.0.0/).

The commit message should be structured as follows: ```<type>: <description>```

Some examples: 
  * If its a new feature: ```feat: <my new feature description>```
  * If its a fix: ```fix: <the fix description>```
  * If its a test: ```test: <the test description>```

### Test : 

You must test what you are developing by creating unit or functional test.
Please keep the code coverage to 100%.

You can create a code coverage report with this command : 
```php
php ./vendor/bin/phunit --coverage-html public/tests
```

and then access the report here : [https://127.0.0.1:8000/tests/](https://127.0.0.1:8000/tests/)


### Pull Request: 

When you are down developing something, you can create a pull request so we'll be able to review your code :

  * Make sure you PR will be merged into [dev](https://github.com/mathiiii-dev/TodoList/tree/dev) first.
  * Add a reviewer to notify that the PR is ready to be reviewed
  * You can also use the tag [RFR](https://github.com/mathiiii-dev/TodoList/labels/RFR) (Ready for review)
  * And wait for someone the review your code and to merge it !

## Quality

This is the quality process to use as well as the rules to follow.

Since this project is made with PHP / Symfony, we try to respect as much as possible the PSR.

For this you can use some tools during development:

### PHP CS Fixer

PHP CS Fixer fixes your code to follow standards.

Run : ```php ./vendor/bin/php-cs-fixer fix src```

This will reformat your code to respect the PSR.

### Codacy

When you commit something or you create a PR, [Codacy](https://app.codacy.com/gh/mathiiii-dev/TodoList/dashboard) will analyse the code and return you a mark depending on your code quality.
If your code isn't correct, it will give you issues. Please correct the issues first before starting anything else. 

And keep the mark as an A (the highest mark).

Thank you to follow this guideline and making TodoList better! 
