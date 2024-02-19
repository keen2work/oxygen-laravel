# Troubleshooting Guide and Common Issues

### Test Errors

1. **Test Error: Did not see expected text [Laravel] within element [body].**

	- If you see this, or errors, check if you're still using a cached environment. Use `php artisan config:cache` to clear cache and try again.

### Build Pipeline Errors

1. If a build pipeline fails, first check the actual root cause of the issue. You'll have to see the errors on the pipeline and then try to re-produce the error locally.
2. Common cause of errors are

	- Out of sync files with Laravel source. See 'Sync with Source' instructions on [CONTRIBUTING.md](CONTRIBUTING.md).
	- A dependent library was updated. Check the first error date for this.
