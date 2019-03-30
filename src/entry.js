jQuery(document).ready(function ($) {

    var AWS = require("aws-sdk/dist/aws-sdk");
var AmazonCognitoIdentity = require('amazon-cognito-identity-js');
var CognitoUserPool = AmazonCognitoIdentity.CognitoUserPool;

var authenticationData = {
    Username: cta_alis_user_info.username,
    Password: cta_alis_user_info.password,
};
var authenticationDetails = new AmazonCognitoIdentity.AuthenticationDetails(authenticationData);
var poolData = { UserPoolId : 'ap-northeast-1_HNT0fUj4J',
    ClientId : '2gri5iuukve302i4ghclh6p5rg'
};
var userPool = new AmazonCognitoIdentity.CognitoUserPool(poolData);
var userData = {
    Username: cta_alis_user_info.username,
    Pool : userPool
};

var cognitoUser = new AmazonCognitoIdentity.CognitoUser(userData);
cognitoUser.authenticateUser(authenticationDetails, {
    onSuccess: function (result) {
        var accessToken = result.getAccessToken().getJwtToken();

        /* Use the idToken for Logins Map when Federating User Pools with identity pools or when passing through an Authorization Header to an API Gateway Authorizer */
        var idToken = result.idToken.jwtToken;

        var data = {
            'action': 'getToken',
            'idToken': idToken
        };

        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post(ajaxurl, data, function (response) {
            alert('Got this from the server: ' + response);
        });

    },

    onFailure: function(err) {
        console.log(err);
        alert(err);
    },

});
});
