# Authentication Flow Clients

This document lists the first rough version of the authentication flow to be taken by the client. This document will be updated during development.

## Registration
1. Ask data
2. Send data to /api/vx/auth/register
3. Show intermediate screen: "Validate email and re-enter password"
4. Send data to /api/vx/auth/login
5. Token is returned, user is registered and logged in

## Login
1. Ask data
2. Send data to /api/vx/auth/login
3. Token is returned, user is logged in

## Token refreshing
1. Send data request and receive 401
2. Send request to /api/vx/auth/refresh
3.a If 401 received: remove all saved data and go to login screen, user is logged out
3.b Else token is returned, resend initial request with new token

## Logout
1. Send request to /api/vx/auth/logout
2. Remove token and saved data
3. Redirect to login screen, user is logged out
