# The content schema

This GraphQL schema exposes the eZ Platform Repository with a structure
similar to that of the [Public API](https://doc.ezplatform.com/en/latest/api/public_php_api).
It gives access to Content, Fields, Locations, Content types... It can be accessed through
`https://<host>/graphql`, as well as `http://<host>/api/ibexa/v3/graphql/graphiql`, API
Platform's bundled GraphiQL IDE pointed at this endpoint (available in the `dev`
environment).

Items from this schema are accessible from the `_repository` root namespace:

## Examples

### List content types and their fields
```
{
    _repository {
        contentTypes
        {
            identifier
            groups {
              identifier
            }
            fieldDefinitions {
              identifier
              fieldTypeIdentifier
            }  
        }
    }
}
```
