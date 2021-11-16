<?php
$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->in(getcwd())
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$config = new PhpCsFixer\Config();

return $config
    ->setUsingCache(true)
    ->setRules([
        // RISKY: Converts simple usages of `array_push($x, $y);` to `$x[] = $y;`.
        'array_push' => true,
        // RISKY: Replace non multibyte-safe functions with corresponding mb function.
        'mb_str_functions' => true,
        // RISKY: Master functions shall be used instead of aliases.
        'no_alias_functions' => true,
        // RISKY: Replaces `rand`, `srand`, `getrandmax` functions calls with their `mt_*` analogs.
        'random_api_migration' => true,
        // RISKY: Cast shall be used, not `settype`.
        'set_type_to_cast' => true,
        // Either language construct `print` or `echo` should be used.
        'no_mixed_echo_print' => true,
        // PHP arrays should be declared using the configured syntax.
        'array_syntax' => true,
        // Operator `=>` should not be surrounded by multi-line whitespaces.
        'no_multiline_whitespace_around_double_arrow' => true,
        // PHP single-line arrays should not have trailing comma.
        'no_trailing_comma_in_singleline_array' => true,
        // In array declaration, there MUST NOT be a whitespace before each comma.
        'no_whitespace_before_comma_in_array' => true,
        // Array index should always be written by using square braces.
        'normalize_index_brace' => true,
        // Arrays should be formatted like function/method arguments, without leading or trailing single line space.
        'trim_array_spaces' => true,
        // In array declaration, there MUST be a whitespace after each comma.
        'whitespace_after_comma_in_array' => true,
        // The body of each structure MUST be enclosed by braces. Braces should be properly placed. Body of braces should be properly indented.
        'braces' => true,
        // PHP code MUST use only UTF-8 without BOM (remove BOM).
        'encoding' => true,
        // The PHP constants `true`, `false`, and `null` MUST be written using the correct casing.
        'constant_case' => true,
        // PHP keywords MUST be in lower case.
        'lowercase_keywords' => true,
        // Class static references `self`, `static` and `parent` MUST be in lower case.
        'lowercase_static_reference' => true,
        // Magic constants should be referred to using the correct casing.
        'magic_constant_casing' => true,
        // Magic method definitions and calls must be using the correct casing.
        'magic_method_casing' => true,
        // Function defined by PHP should be called using the correct casing.
        'native_function_casing' => true,
        // Native type hints for functions should use the correct case.
        'native_function_type_declaration_casing' => true,
        // A single space or none should be between cast and variable.
        'cast_spaces' => true,
        // Cast should be written in lower case.
        'lowercase_cast' => true,
        // RISKY: Replaces `intval`, `floatval`, `doubleval`, `strval` and `boolval` function calls with according type casting operator.
        'modernize_types_casting' => true,
        // Short cast `bool` using double exclamation mark should not be used.
        'no_short_bool_cast' => true,
        // Variables must be set `null` instead of using `(unset)` casting.
        'no_unset_cast' => true,
        // Cast `(boolean)` and `(integer)` should be written as `(bool)` and `(int)`, `(double)` and `(real)` as `(float)`, `(binary)` as `(string)`.
        'short_scalar_cast' => true,
        // Class, trait and interface elements must be separated with one blank line.
        'class_attributes_separation' => true,
        // Whitespace around the keywords of a class, trait or interfaces definition should be one space.
        'class_definition' => true,
        // There should be no empty lines after class opening brace.
        'no_blank_lines_after_class_opening' => true,
        // Properties MUST not be explicitly initialized with `null` except when they have a type declaration (PHP 7.4).
        'no_null_property_initialization' => true,
        // Orders the elements of classes/interfaces/traits.
        'ordered_class_elements' => true,
        // Converts `protected` variables and methods to `private` where possible.
        'protected_to_private' => true,
        // Inside a `final` class or anonymous class `self` should be preferred to `static`.
        'self_static_accessor' => true,
        // There MUST NOT be more than one property or constant declared per statement.
        'single_class_element_per_statement' => true,
        // Each trait `use` must be done as single statement.
        'single_trait_insert_per_statement' => true,
        // Visibility MUST be declared on all properties and methods; `abstract` and `final` MUST be declared before the visibility; `static` MUST be declared after the visibility.
        'visibility_required' => true,
        // DocBlocks must start with two asterisks, multiline comments must start with a single asterisk, after the opening slash. Both must end with a single asterisk before the closing slash.
        'multiline_comment_opening_closing' => true,
        // There should not be any empty comments.
        'no_empty_comment' => true,
        // There MUST be no trailing spaces inside comment or PHPDoc.
        'no_trailing_whitespace_in_comment' => true,
        // Single-line comments and multi-line comments with only one line of actual content should use the `//` syntax.
        'single_line_comment_style' => ['comment_types' => ['hash']],
        // The keyword `elseif` should be used instead of `else if` so that all control keywords look like single words.
        'elseif' => true,
        // Include/Require and file path should be divided with a single space. File path should not be placed under brackets.
        'include' => true,
        // Replace control structure alternative syntax to use braces.
        'no_alternative_syntax' => true,
        // There must be a comment when fall-through is intentional in a non-empty case body.
        'no_break_comment' => true,
        // Replaces superfluous `elseif` with `if`.
        'no_superfluous_elseif' => true,
        // Remove trailing commas in list function calls.
        'no_trailing_comma_in_list_call' => true,
        // Removes unneeded parentheses around control statements.
        'no_unneeded_control_parentheses' => true,
        // Removes unneeded curly braces that are superfluous and aren't part of a control structure's body.
        'no_unneeded_curly_braces' => true,
        // There should not be useless `else` cases.
        'no_useless_else' => true,
        // Simplify `if` control structures that return the boolean result of their condition.
        'simplified_if_return' => true,
        // A case should be followed by a colon and not a semicolon.
        'switch_case_semicolon_to_colon' => true,
        // Removes extra spaces between colon and case value.
        'switch_case_space' => true,
        // Switch case must not be ended with `continue` but with `break`.
        'switch_continue_to_break' => true,
        // Multi-line arrays, arguments list and parameters list must have a trailing comma.
        'trailing_comma_in_multiline' => true,
        // Doctrine annotations must use configured operator for assignment in arrays.
        'doctrine_annotation_array_assignment' => true,
        // Doctrine annotations without arguments must use the configured syntax.
        'doctrine_annotation_braces' => true,
        // Doctrine annotations must be indented with four spaces.
        'doctrine_annotation_indentation' => true,
        // Fixes spaces in Doctrine annotations.
        'doctrine_annotation_spaces' => true,
        // Spaces should be properly placed in a function declaration.
        'function_declaration' => true,
        // Ensure single space between function's argument and its typehint.
        'function_typehint_space' => true,
        // Lambda must not import variables it doesn’t use.
        'lambda_not_used_import' => true,
        // In method arguments and method call, there MUST NOT be a space before each comma and there MUST be one space after each comma. Argument lists MAY be split across multiple lines, where each subsequent line is indented once. When doing so, the first item in the list MUST be on the next line, and there MUST be only one argument per line.
        'method_argument_space' => true,
        // RISKY: Add leading `\` before function invocation to speed up resolving.
        'native_function_invocation' => true,
        // When making a method or function call, there MUST NOT be a space between the method or function name and the opening parenthesis.
        'no_spaces_after_function_name' => true,
        // Adds or removes `?` before type declarations for parameters with a default `null` value.
        'nullable_type_declaration_for_default_null_value' => true,
        // There should be one or no space before colon, and one space after it in return type declarations, according to configuration.
        'return_type_declaration' => true,
        // Throwing exception must be done in single line.
        'single_line_throw' => true,
        // Transforms imported FQCN parameters and return types in function arguments to short version.
        'fully_qualified_strict_types' => true,
        // Imports or fully qualifies global classes/functions/constants.
        'global_namespace_import' => true,
        // Remove leading slashes in `use` clauses.
        'no_leading_import_slash' => true,
        // Unused `use` statements must be removed.
        'no_unused_imports' => true,
        // Ordering `use` statements.
        'ordered_imports' => true,
        // There MUST be one use keyword per declaration.
        'single_import_per_statement' => true,
        // Each namespace use MUST go on its own line and there MUST be one blank line after the use statements block.
        'single_line_after_imports' => true,
        // Using `isset($var) &&` multiple times should be done in one call.
        'combine_consecutive_issets' => true,
        // Calling `unset` on multiple items should be done in one call.
        'combine_consecutive_unsets' => true,
        // Equal sign in declare statement should be surrounded by spaces or not following configuration.
        'declare_equal_normalize' => true,
        // Add curly braces to indirect variables to make them clear to understand. Requires PHP >= 7.0.
        'explicit_indirect_variable' => true,
        // Replaces `is_null($var)` expression with `null === $var`.
        'is_null' => true,
        // Ensures a single space after language constructs.
        'single_space_after_construct' => true,
        // List (`array` destructuring) assignment should be declared using the configured syntax. Requires PHP >= 7.1.
        'list_syntax' => true,
        // There MUST be one blank line after the namespace declaration.
        'blank_line_after_namespace' => true,
        // Namespace must not contain spacing, comments or PHPDoc.
        'clean_namespace' => true,
        // There should be no blank lines before a namespace declaration.
        'no_blank_lines_before_namespace' => true,
        // The namespace declaration line shouldn't contain leading whitespace.
        'no_leading_namespace_whitespace' => true,
        // Binary operators should be surrounded by space as configured.
        'binary_operator_spaces' => true,
        // Concatenation should be spaced according configuration.
        'concat_space' => true,
        // Pre- or post-increment and decrement operators should be used if possible.
        'increment_style' => true,
        // All instances created with new keyword must be followed by braces.
        'new_with_braces' => true,
        // Logical NOT operators (!) should have leading and trailing whitespaces.
        'not_operator_with_space' => true,
        // There should not be space before or after object operators `->` and `?->`.
        'object_operator_without_whitespace' => true,
        // Operators - when multiline - must always be at the beginning or at the end of the line.
        'operator_linebreak' => true,
        // Increment and decrement operators should be used if possible.
        'standardize_increment' => true,
        // Replace all `<>` with `!=`.
        'standardize_not_equals' => true,
        // Standardize spaces around ternary operator.
        'ternary_operator_spaces' => true,
        // Use `null` coalescing operator `??` where possible. Requires PHP >= 7.0.
        'ternary_to_null_coalescing' => true,
        // Unary operators should be placed adjacent to their operands.
        'unary_operator_spaces' => true,
        // Ensure there is no code on the same line as the PHP open tag and it is followed by a blank line.
        'blank_line_after_opening_tag' => true,
        // Replace short-echo `<?=` with long format `<?php echo` syntax.
        'echo_tag_syntax' => true,
        // PHP code must use the long `<?php` tags or short-echo `<?=` tags and not other tag variations.
        'full_opening_tag' => true,
        // Ensure there is no code on the same line as the PHP open tag.
        'linebreak_after_opening_tag' => true,
        // The closing `? >` tag MUST be omitted from files containing only PHP.
        'no_closing_tag' => true,
        // PHPUnit annotations should be a FQCNs including a root namespace.
        'php_unit_fqcn_annotation' => true,
        // All PHPUnit test classes should be marked as internal.
        'php_unit_internal_class' => true,
        // Enforce camel (or snake) case for PHPUnit test methods, following configuration.
        'php_unit_method_casing' => true,
        // All PHPUnit test cases should have `@small`, `@medium` or `@large` annotation to enable run time limits.
        'php_unit_size_class' => true,
        // Adds a default `@coversNothing` annotation to PHPUnit test classes that have no `@covers*` annotation.
        'php_unit_test_class_requires_covers' => true,
        // Each line of multi-line DocComments must have an asterisk [PSR-5] and must be aligned with the first one.
        'align_multiline_comment' => true,
        // Configured annotations should be omitted from PHPDoc.
        'general_phpdoc_annotation_remove' => true,
        // Renames PHPDoc tags.
        'general_phpdoc_tag_rename' => true,
        // There should not be blank lines between docblock and the documented element.
        'no_blank_lines_after_phpdoc' => true,
        // There should not be empty PHPDoc blocks.
        'no_empty_phpdoc' => true,
        // Removes @param, @return and @var tags that don’t provide any useful information.
        'no_superfluous_phpdoc_tags' => true,
        // PHPDoc should contain `@param` for all params.
        'phpdoc_add_missing_param_annotation' => true,
        // All items of the given phpdoc tags must be either left-aligned or (by default) aligned vertically.
        'phpdoc_align' => true,
        // PHPDoc annotation descriptions should not be a sentence.
        'phpdoc_annotation_without_dot' => true,
        // Docblocks should have the same indentation as the documented subject.
        'phpdoc_indent' => true,
        // Fixes PHPDoc inline tags.
        'phpdoc_inline_tag_normalizer' => true,
        // Changes doc blocks from single to multi line, or reversed. Works for class constants, properties and methods only.
        'phpdoc_line_span' => true,
        // `@access` annotations should be omitted from PHPDoc.
        'phpdoc_no_access' => true,
        // No alias PHPDoc tags should be used.
        'phpdoc_no_alias_tag' => true,
        // `@return void` and `@return null` annotations should be omitted from PHPDoc.
        'phpdoc_no_empty_return' => true,
        // `@package` and `@subpackage` annotations should be omitted from PHPDoc.
        'phpdoc_no_package' => true,
        // Classy that does not inherit must not have `@inheritdoc` tags.
        'phpdoc_no_useless_inheritdoc' => true,
        // Order phpdoc tags by value.
        'phpdoc_order_by_value' => true,
        // Annotations in PHPDoc should be ordered so that `@param` annotations come first, then `@throws` annotations, then `@return` annotations.
        'phpdoc_order' => true,
        // The type of `@return` annotations of methods returning a reference to itself must the configured one.
        'phpdoc_return_self_reference' => true,
        // Scalar types should always be written in the same form. `int` not `integer`, `bool` not `boolean`, `float` not `real` or `double`.
        'phpdoc_scalar' => true,
        // Annotations in PHPDoc should be grouped together so that annotations of the same type immediately follow each other, and annotations of a different type are separated by a single blank line.
        'phpdoc_separation' => true,
        // Single line `@var` PHPDoc should have proper spacing.
        'phpdoc_single_line_var_spacing' => true,
        // PHPDoc summary should end in either a full stop, exclamation mark, or question mark.
        'phpdoc_summary' => true,
        // Fixes casing of PHPDoc tags.
        'phpdoc_tag_casing' => true,
        // Forces PHPDoc tags to be either regular annotations or inline.
        'phpdoc_tag_type' => true,
        // Docblocks should only be used on structural elements.
        'phpdoc_to_comment' => true,
        // Removes extra blank lines after summary and after description in PHPDoc.
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        // PHPDoc should start and end with content, excluding the very first and last line of the docblocks.
        'phpdoc_trim' => true,
        // The correct case must be used for standard PHP types in PHPDoc.
        'phpdoc_types' => true,
        // Sorts PHPDoc types.
        'phpdoc_types_order' => true,
        // `@var` and `@type` annotations must have type and name in the correct order.
        'phpdoc_var_annotation_correct_order' => true,
        // `@var` and `@type` annotations of classy properties should not contain the name.
        'phpdoc_var_without_name' => true,
        // There should not be an empty `return` statement at the end of a function.
        'no_useless_return' => true,
        // Local, dynamic and directly referenced variables should not be assigned and directly returned by a function or method.
        'return_assignment' => true,
        // A return statement wishing to return `void` should not return `null`.
        'simplified_null_return' => true,
        // Forbid multi-line whitespace before the closing semicolon or move the semicolon to the new line for chained calls.
        'multiline_whitespace_before_semicolons' => true,
        // Remove useless semicolon statements.
        'no_empty_statement' => true,
        // Single-line whitespace before closing semicolon are prohibited.
        'no_singleline_whitespace_before_semicolons' => true,
        // Instructions must be terminated with a semicolon.
        'semicolon_after_instruction' => true,
        // Fix whitespace after a semicolon.
        'space_after_semicolon' => true,
        // Escape implicit backslashes in strings and heredocs to ease the understanding of which are special chars interpreted by PHP and which not.
        'escape_implicit_backslashes' => true,
        // Converts implicit variables into explicit ones in double-quoted strings or heredoc syntax.
        'explicit_string_variable' => true,
        // Convert `heredoc` to `nowdoc` where possible.
        'heredoc_to_nowdoc' => true,
        // There should not be a binary flag before strings.
        'no_binary_string' => true,
        // Converts explicit variables in double-quoted strings and heredoc syntax from simple to complex format (`${` to `{$`).
        'simple_to_complex_string_variable' => true,
        // Convert double quotes to single quotes for simple strings.
        'single_quote' => true,
        // Each element of an array must be indented exactly once.
        'array_indentation' => true,
        // An empty line feed must precede any configured statement.
        'blank_line_before_statement' => true,
        // Remove extra spaces in a nullable typehint.
        'compact_nullable_typehint' => true,
        // Heredoc/nowdoc content must be properly indented. Requires PHP >= 7.3.
        'heredoc_indentation' => true,
        // Code MUST use configured indentation type.
        'indentation_type' => true,
        // All PHP files must use same line ending.
        'line_ending' => true,
        // Method chaining MUST be properly indented. Method chaining with different levels of indentation is not supported.
        'method_chaining_indentation' => true,
        // Removes extra blank lines and/or blank lines following configuration.
        'no_extra_blank_lines' => true,
        // There MUST NOT be spaces around offset braces.
        'no_spaces_around_offset' => true,
        // There MUST NOT be a space after the opening parenthesis. There MUST NOT be a space before the closing parenthesis.
        'no_spaces_inside_parenthesis' => true,
        // Remove trailing whitespace at the end of non-blank lines.
        'no_trailing_whitespace' => true,
        // Remove trailing whitespace at the end of blank lines.
        'no_whitespace_in_blank_line' => true,
        // A PHP file without end tag must always end with a single empty line feed.
        'single_blank_line_at_eof' => true,
    ])
    ->setFinder($finder);
