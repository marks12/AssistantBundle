# About
Symfony AssistantBundle helps configure new application and add 9 types of associations:

1. Many-To-One, Unidirectional,
1. One-To-One, Unidirectional,
1. One-To-One, Bidirectional,
1. One-To-One, Self-referencing,
1. One-To-Many, Bidirectional,
1. One-To-Many, Unidirectional with Join Table,
1. One-To-Many, Self-referencing,
1. Many-to-Many, Unidirectional,
1. Many-to-Many, Bidirectional

# Install 

``composer require marks12/assistantbundle``

### Add to modules list

```php
        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            ...            
            $bundles[] = new Marks12\AssistantBundle\Marks12AssistantBundle();
            ...            
        }
```

# Using

``bin/console`` 