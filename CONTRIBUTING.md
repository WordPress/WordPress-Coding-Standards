### 📘 Writing XML Documentation for Sniffs

When adding XML docs inside sniff files:

- Use `<em>Valid:</em>` and `<em>Invalid:</em>` to highlight examples.
- Code blocks should follow the WordPress Coding Standards.
- Example:

  ```php
  /**
   * This sniff checks for X.
   *
   * <em>Valid:</em>
   * ```php
   * do_something();
   * ```
   *
   * <em>Invalid:</em>
   * ```php
   * bad_sniff();
   * ```
   */
