# PR Agent Configuration for testcontainers-php

This document describes how to configure PR Agent for the testcontainers-php project.

## Overview

PR Agent is an AI-powered tool that automates pull request analysis, feedback, and suggestions. It can be configured to provide detailed reviews, generate PR descriptions, and offer code improvement suggestions.

## Configuration File

PR Agent is configured using a `.pr_agent.toml` file in the root of the repository. This file contains various sections that control different aspects of PR Agent's behavior.

## Key Configuration Sections

### GitHub App Configuration

```toml
[github_app]
pr_commands = [
    "/describe",
    "/review",
    "/review auto_approve",
    "/improve",
]
```

This section defines the commands that PR Agent will respond to in GitHub comments.

### Auto-Approval Configuration

Auto-approval is a feature that allows PR Agent to automatically approve pull requests under specific conditions. For safety reasons, this feature is disabled by default.

```toml
[config]
enable_auto_approval = true  # For criteria-based auto-approval
enable_comment_approval = true  # For approval via comments
```

**Important**: Auto-approval settings must be in the `[config]` section, not in the `[pr_reviewer]` section. This is a critical configuration detail that can cause the feature to not work if placed incorrectly.

### PR Description Settings

```toml
[pr_description]
publish_labels=true
publish_description_as_comment=true
generate_ai_title=true
final_update_message=true
```

These settings control how PR Agent generates and publishes PR descriptions.

### PR Reviewer Settings

```toml
[pr_reviewer]
enable_review_labels_effort = true
enable_review_labels_security = true
inline_code_comments=false
require_score_review=true
require_can_be_split_review=true
require_estimate_effort_to_review=true
require_tests_review=true
require_security_review=true
```

These settings control the behavior of the PR review functionality.

### Custom Review Instructions

The `extra_instructions` parameter in the `[pr_reviewer]` section allows you to provide custom instructions to guide the AI reviewer. For testcontainers-php, these instructions should include:

```toml
extra_instructions="""
Review this project with the following key points in mind:
1. PHP Compatibility: This library supports PHP versions 5.6 to 8.3, including EOL versions. Code must be compatible with PHP 5.6.
2. Purpose: testcontainers-php manages Docker containers for PHP test environments, providing consistent and isolated test dependencies.
3. Design Principles: 
   - Minimal dependencies (mainly symfony/process and psr/log)
   - Trait-based composition for container settings
   - Type-safe value objects for domain concepts
4. Coding Standards:
   - Use PHPDoc for type information (not type declarations for PHP 5.6 compatibility)
   - Follow K&R style bracing (braces on same line)
   - Use 4-space indentation
   - Prefer fluent interfaces and method chaining
5. Error Handling: Ensure proper exception handling and resource cleanup
"""
```

These instructions provide the AI reviewer with the essential context needed to evaluate pull requests effectively.

### PR Code Suggestions Settings

```toml
[pr_code_suggestions]
focus_only_on_problems=true
num_code_suggestions_per_chunk="3"
commitatable_code_suggestions=false
```

These settings control how PR Agent generates code improvement suggestions.

### Best Practices

```toml
[best_practices]
enable_global_best_practices = true
```

This setting enables checking PRs against global best practices.

## Recommended Configuration for testcontainers-php

The recommended PR Agent configuration for testcontainers-php is:

```toml
[github_app]
pr_commands = [
    "/describe",
    "/review",
    "/review auto_approve",
    "/improve",
]

[config]
enable_auto_approval = true  # For criteria-based auto-approval
enable_comment_approval = true  # For approval via comments

[pr_description]
publish_labels=true
publish_description_as_comment=true
generate_ai_title=true
final_update_message=true

[pr_reviewer]
enable_review_labels_effort = true
enable_review_labels_security = true
inline_code_comments=false
require_score_review=true
require_can_be_split_review=true
require_estimate_effort_to_review=true
require_tests_review=true
require_security_review=true
extra_instructions="""
Review this project with the following key points in mind:
1. PHP Compatibility: This library supports PHP versions 5.6 to 8.3, including EOL versions. Code must be compatible with PHP 5.6.
2. Purpose: testcontainers-php manages Docker containers for PHP test environments, providing consistent and isolated test dependencies.
3. Design Principles: 
   - Minimal dependencies (mainly symfony/process and psr/log)
   - Trait-based composition for container settings
   - Type-safe value objects for domain concepts
4. Coding Standards:
   - Use PHPDoc for type information (not type declarations for PHP 5.6 compatibility)
   - Follow K&R style bracing (braces on same line)
   - Use 4-space indentation
   - Prefer fluent interfaces and method chaining
5. Error Handling: Ensure proper exception handling and resource cleanup
"""

[pr_code_suggestions]
focus_only_on_problems=true
num_code_suggestions_per_chunk="3"
commitatable_code_suggestions=false

[best_practices]
enable_global_best_practices = true
```

This configuration ensures that PR Agent provides comprehensive PR reviews with security checks, test coverage verification, and effort estimation, while also enabling auto-approval features.
