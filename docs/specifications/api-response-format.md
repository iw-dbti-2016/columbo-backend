# API Response Format

Because this complete application is based around the API, it seems appropriate to define an application-wide JSON-format for all responses.

In this section, this format will be defined. **Be aware that updates may occur during development.** At a later stage, this entire section along with other technical definitions (such as the expected auth-flow) will be extracted to a different file, more appropriate for these kinds of discussions (i.e. some sort of documentation).

First of all, an appropriate HTTP statuscode must be included in every response, this statuscode is the start for any connecting instances. The format of the response might change based on the statuscode. We try to keep this to a minimum.

Generally, the `success`-attribute is required for every response, a `message`-attribute is optional and describes the result or performed action. If the `success`-attribute is `true`, the `data`-attribute is required, though it must not contain anything. When `success` is `false`, data must not be present, and clients may ignore it completely.

## GET single item - 200 (OK)

When getting a single item, the `data`-attribute contains that one item directly.

```JSON
{
	"success": true,
	"data": {
		"id": 1,
		"name": "Jane Doe",
		"email": "jane@example.com",
	},
}
```

## GET multiple items - 200 (OK)

When getting multiple items, the `data`-attribute contains an array of items.

```JSON
{
	"success": true,
	"data": [
		{
			"id": 1,
			"name": "Jane Doe",
			"email": "jane@example.com",
		},
		{
			"id": 2,
			"name": "John Doe",
			"email": "john@example.com",
		},
	],
}
```

## POST - 201 (Created)

Upon creation, the created items are returned. **This will always be an array, even if only one item has been created.**

```JSON
{
	"success": true,
	"message": "Success (or any optional custom message)",
	"data": [
		{
			"id": 3,
			"name": "Dan Doe",
			"email": "dan@example.com",
		},
		{
			"id": 4,
			"name": "Daisy Doe",
			"email": "daisy@example.com",
		},
	],
}
```

## PATCH - 200 (OK)

On successful update, the updated item is returned directly.

```JSON
{
	"success": true,
	"message": "entity updated",
	"data": {
		"id": 1,
		"name": "Janet Doe",
		"email": "janet@example.com",
	},
}
```

## DELETE - 200 (OK)

No data is supplied upon deletion.

```JSON
{
	"success": true,
	"message": "entity deleted",
	"data": [],
}
```

## POST/PATCH - 422 (Unprocessable Entity)

When validation fails, a 422 will be returned, with the `errors`-attribute containing the errors in the data. They're grouped by field. Fields without errors are not present in the `errors`-attribute. Per field an array with messages is present.

```JSON
{
	"success": false,
	"message": "Validation Failed",
	"errors": {
		"name": [
			"This field is required",
		],
	}
}
```

## GET/POST/PATCH/PUT/DELETE - 404 (Not Found)

The client can recognise this response by code and can show the message if desired, otherwise show a custom message.

```JSON
{
	"success": false,
	"message": "Not Found",
}
```

## 401 (Unauthorized)

If the client is not authenticated, this response will be returned. The `message`-attribute explains why access was denied. The client will have to try to refresh it's JWT-token. If that refresh-request still results in the 401-response, the user has to relogin.

```JSON
{
	"success": false,
	"message": "token expired",
}
```

## 403 (Forbidden)

If a user does not have sufficient permissions to view the requested item(s) this request will be returned.

```JSON
{
	"success": false,
	"message": "Access denied",
}
```

*Note that 401 and 403 are used a little differently than their names indicate (basically as 'Unauthenticated' and 'Unauthorized' respectively). This is completely intentionally. A full explanation can be found in [this stackoverflow answer](https://stackoverflow.com/a/6937030). These status codes are explained in [RFC7235](https://tools.ietf.org/html/rfc7235#section-3.1) and [RFC7231](https://tools.ietf.org/html/rfc7231#section-6.5.3) that make RFC2616, which is a little less clear about the distinction, obsolete.*

## 500 (Internal Server Error)

If there is a problem with the server, this response is provided.

```JSON
{
	"success": false,
	"message": "Server Error",
}
```
