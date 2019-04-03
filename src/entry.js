function cta_alis_user_info() {

    // var AWS = require("aws-sdk/dist/aws-sdk");
    var AmazonCognitoIdentity = require('amazon-cognito-identity-js');
    // var CognitoUserPool = AmazonCognitoIdentity.CognitoUserPool;

    var alisUsername = prompt("To Publish this post in Alis, enter your Alis Username");
    var alisPassword = prompt("Also enter Alis Password");

    var authenticationData = {
        Username: alisUsername,
        Password: alisPassword,
    };
    var authenticationDetails = new AmazonCognitoIdentity.AuthenticationDetails(authenticationData);
    var poolData = {
        UserPoolId: 'ap-northeast-1_HNT0fUj4J',
        ClientId: '2gri5iuukve302i4ghclh6p5rg'
    };
    var userPool = new AmazonCognitoIdentity.CognitoUserPool(poolData);
    var userData = {
        Username: alisUsername,
        Pool: userPool
    };
    var cognitoUser = new AmazonCognitoIdentity.CognitoUser(userData);
    cognitoUser.authenticateUser(authenticationDetails, {
        onSuccess: function (result) {

            var idToken = result.idToken.jwtToken;
        },

        onFailure: function (err) {
            alert(err);
        },

    });

}

var publishBtn = document.getElementsByClassName('editor-post-publish-panel__toggle')[0];
publishBtn.addEventListener("click", cta_alis_user_info, false);
