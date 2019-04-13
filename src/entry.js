const {__} = wp.i18n;

window.addEventListener('DOMContentLoaded', () => {

    let publishBtn = document.getElementsByClassName('editor-post-publish-panel__toggle')[0];
    publishBtn.addEventListener("click", cta_call_api_to_share_post, false);
    publishBtn.addEventListener("click", cta_display_prompt, false);

});

const cta_display_prompt = () => {

    let alisUsername = prompt(__('To Publish this post in Alis, enter your Alis Username', 'connect-to-alis'));
    let alisPassword = prompt(__('Also enter Alis Password', 'connect-to-alis'));

    if (alisUsername && alisPassword) {
        alert(__('This post will also share in Alis. Are you OK about that?', 'connect-to-alis'));
        cta_get_token(alisUsername, alisPassword);
    }
};

const cta_get_token = (alisUsername, alisPassword) => {

    let AmazonCognitoIdentity = require('amazon-cognito-identity-js');

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

        },

        onFailure: (err) => {
            alert("Fail to share your post in Alis. Something is wrong." + err);
        },

    });
};
