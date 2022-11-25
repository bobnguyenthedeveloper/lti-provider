###A Laravel example of LTI Tool Provider (Tested with Moodle LMS)

To run the project, follow these steps:
1. Clone the source code
2. Run ```composer update```
3. Clone the .env from .env.example and edit the database information
4. Run migration: ```php artisan migrate```
5. On LMS create a tool provider with the following information:
   - Tool settings
     - Tool URL: APP_URL 
     - Public keyset: APP_URL/jwks 
     - Initiate login URL: APP_URL/lti-login 
     - Redirection URI(s): APP_URL/redirector 
     - Support Deep Linking (Content-Item Message): On 
   - Services
     - IMS LTI Assignment and Grade Services: Use this service for grade sync and column management 
     - IMS LTI Names and Role Provisioning: Use this service to retrieve members' information as per privacy settings
   - Privacy
     - Share launcher's name with tool: Always
     - Share launcher's email with tool: Always
     - Accept grades from the tool: Always
6. On the created tool, click on "View configuration details", copy the values there and paste to RegistrationSeeder as following:
    - Platform ID: issuer
    - Client ID: client_id
    - Deployment ID: deployment_id
    - Public keyset URL: jwks_endpoint
    - Access token URL: auth_token_endpoint
    - Authentication request URL: auth_login_endpoint
7. Run the seed: ```php artisan db:seed```
8. Go to LMS, create a new external service activity. In the activity setting, choose the preconfigured tool. Then, click on the "Select content" button. Choose the level as you want.
9. Login to student account, then click the activity.
