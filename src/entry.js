const {__} = wp.i18n;
const AmazonCognitoIdentity = require('amazon-cognito-identity-js');

window.addEventListener('DOMContentLoaded', () => {

    class CtaAlisApi {

        constructor() {

            let publishBtn = document.getElementsByClassName('editor-post-publish-panel__toggle')[0];
            publishBtn.addEventListener("click", ()=>{this.displayPrompt()}, false);
        }

        displayPrompt() {

            let alisUsername = prompt(__('To Publish this post in Alis, enter your Alis Username', 'connect-to-alis'));
            let alisPassword = prompt(__('Also enter Alis Password', 'connect-to-alis'));

            if (alisUsername && alisPassword) {
                alert(__('This post will also share in Alis. Are you OK about that?', 'connect-to-alis'));
                this.getToken(alisUsername, alisPassword);
            }
        }

        getToken(alisUsername, alisPassword) {

            let authenticationData = {
                Username: alisUsername,
                Password: alisPassword,
            };
            let authenticationDetails = new AmazonCognitoIdentity.AuthenticationDetails(authenticationData);
            let poolData = {
                UserPoolId: 'ap-northeast-1_HNT0fUj4J',
                ClientId: '2gri5iuukve302i4ghclh6p5rg'
            };
            let userPool = new AmazonCognitoIdentity.CognitoUserPool(poolData);
            let userData = {
                Username: alisUsername,
                Pool: userPool
            };
            let cognitoUser = new AmazonCognitoIdentity.CognitoUser(userData);
            cognitoUser.authenticateUser(authenticationDetails, {
                onSuccess: (result) => {

                    let idToken = result.idToken.jwtToken;

                    console.log(idToken);

                },

                onFailure: (err) => {
                    alert("Fail to share your post in Alis. Something is wrong." + err);
                },

            });
        }

        sendApiToServer(token){

        }
    }

    let ctaAlisApi = new CtaAlisApi();

});
