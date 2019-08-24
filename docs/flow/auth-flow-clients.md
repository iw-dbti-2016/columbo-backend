# Authentication Flow Clients

This document lists the first rough version of the authentication flow to be taken by the client. This document will be updated during development.

## Registration
1. Ask data (name, email, username, password, location and optionals)
2. a. Send data to /api/vx/auth/register\
	b. You will receive a 201 response on success
3. Show intermediate screen: "Validate email and re-enter password"
4. Send data to /api/vx/auth/login
5. On success, a 200 response with Token is returned, user is registered and logged in

## Login
1. Ask data (email & password)
2. Send data to /api/vx/auth/login
3. On success, a 200 response with Token is returned, user is logged in

## Sending a data-request

When successfully logging in, following data is returned:

```JSON
{
	"success": true,
	"data": {
		"token": "eYqfqsdfmoijflajfmoijaf.fdjhazmjfamlkjfae.kljfalmjemla",
		"token_type": "bearer",
		"expires_in": 3600,
		"user": {
			"first_name": "John",
			"...": "..."
		},
	},
}
```

When performing subsequent requests, include following content in the `Authorization` header: `{token_type} {token}`. So the above example would result in the following header:\
`Authorization: bearer eYqfqsdfmoijflajfmoijaf.fdjhazmjfamlkjfae.kljfalmjemla`\
(note that this token is just rubbish and an actual token will be longer and [make sense](https://jwt.io/)).

Store the token in a secure location (e.g. secure storage in Android) and do *not* modify it in any way. It is permitted to decode the token (first two parts are base64-encoded), but the original has to be sent back.

## Token refreshing
1. Keep track of the time to expiry of the token.
2. Before expiry, send a PATCH request to /api/vx/auth/refresh
3. a. If 200, use new received token and delete previous\
	b. If 401:  remove all saved data and go to login screen, user is logged out

*Note: Receiving a 403 (Forbidden) is not necessarily incentive to refresh the token. 403 indicates that the authenticated user is not authorized (not allowed by the owner or administrator) to view the requested data.*

## Logout
1. Send delete request to /api/vx/auth/logout
2. Receive 200 and remove token and saved data
3. Redirect to login screen, user is logged out
