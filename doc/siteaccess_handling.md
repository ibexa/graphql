# Siteaccess handling

The GraphQL endpoint is siteaccess aware, following the regular matching, be it URL or host.
GraphiQL (`/api/ibexa/v3/graphql/graphiql`) always queries through the same siteaccess-aware
`/graphql` endpoint, so the siteaccess is resolved from the host/URL used to reach GraphiQL
itself:
- https://admin.host/graphql: will match the admin siteaccess
- https://host/sa/graphql: will match the `sa` siteaccess
- https://host/graphql: will match the default siteaccess

Resolution of values is done using the siteaccess aware Repository services, and will therefore
obey the siteaccess' language priorities.