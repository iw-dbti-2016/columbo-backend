# Authentication Flow Clients

This document lists the first rough version of the authentication flow to be taken by the client. This document will be updated during development.

## Registration

1. Ask data (first name, (middle name, ) last name, username, email, location, password, password confirmation).
2. Send data to `/api/vx/auth/register`.
3. You will receive a 201 response on success, this response includes the user's authentication token and the user-data.
4. Show intermediate screen: "Validate email and refresh," with a refresh button.
5. a. When refresh is clicked, try to boot the application (i.e. request the first required data).\
	b. If this returns a 403, the user's email is not verified and the message has to stay, maybe flash a new message "Refreshing failed" on the screen.
6. When data can be retreived (200), the user has validated his/her email and you can now boot the application.

## Resend Verification Email

1. When step `4` of the previous paragraph (Registration) is shown to the user, include a `resend the email`-button, or alike.
2. When clicking this button, send a POST-request to `/api/vx/auth/email/resend`. Be sure to include the user's valid token to the request, otherwise it is not valid.
3. When receiving a 200, the email has been resent. There is a limit on the number of resends that can be performed (6 every minute). An error will be returned if the limit is exceeded.

## Forgot Password

1. Ask data (email).
2. Send POST-request `/api/vx/auth/password/email`.
3. If a 200 is returned, an email is sent to the user and they have to check their account. Tell them to check spam. Further handling will be on the web, therefore go back to the login-screen.

## Login

1. Ask data (email & password).
2. Send data to `/api/vx/auth/login`
3. a. On success, a 200 response with Token is returned, user is logged in.\
	b. On fail, a 422 is returned, show errors on the screen.

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

## Sending a data-request

When performing subsequent requests, include following content in the `Authorization` header: `{token_type} {token}`. So the above example would result in the following header:\
`Authorization: bearer eYqfqsdfmoijflajfmoijaf.fdjhazmjfamlkjfae.kljfalmjemla`\
(note that this token is just rubbish and an actual token will be longer and [make sense](https://jwt.io/)).

Store the token in a secure location (e.g. secure storage in Android) and do *not* modify it in any way. It is permitted to decode the token (first two parts are base64-encoded), but the original has to be sent back.

## Token refreshing

1. Keep track of the time to expiry of the token.
2. Before expiry, send a PATCH request to `/api/vx/auth/refresh`.
3. a. If 200, use new received token and delete previous.\
	b. If 401, remove all saved data and go to login screen, user is logged out.

*Note: Receiving a 403 (Forbidden) is not necessarily incentive to refresh the token. 403 indicates that the authenticated user is not authorized (not allowed by the owner or administrator) to view the requested data. The may mean that you are trying to fetch data the user cannot access, or after registration it means that the user did not yet verify his/her email.*

## Logout

1. Send delete request to `/api/vx/auth/logout`.
2. Receive 200 and remove token and saved/cached data.
3. Redirect to login screen, user is logged out.
