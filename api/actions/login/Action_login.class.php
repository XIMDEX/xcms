<?php

/**
 *  \details &copy; 2013  Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

require_once(XIMDEX_ROOT_PATH . '/inc/modules/ModulesManager.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/fsutils/FsUtils.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/mvc/Request.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/persistence/Config.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/auth/Authenticator.class.php');
require_once(XIMDEX_ROOT_PATH . '/api/utils/Crypto.class.php');
require_once(XIMDEX_ROOT_PATH . '/conf/stats.conf');
ModulesManager::file('/inc/i18n/I18N.class.php');

//use Swagger\Annotations as SWG;
/**
 * 
 * /**
 * @SWG\Resource(
 *     apiVersion="0.2",
 *     swaggerVersion="1.1",
 *     resourcePath="/login",
 *     basePath="http://test.ximdex.es/ximdex_new/api"
 * )
 * 
 * 
 * @SWG\Api(
 *  path="/login",
 *  description="Login operation",
 *  @SWG\Operations(
 *      @SWG\Operation(
 *          httpMethod="GET",
 *          summary="Performs the login method returning a token",
 *          notes="Returns a JSON containing the generated token",
 *          nickname="login",
 *          responseClass="String",
 *          @SWG\Parameters(
 *              @SWG\Parameter(name="user", description="The name of the user", paramType="query", required="true", allowMultiple="false", dataType="string"),
 *              @SWG\Parameter(name="pass", description="The password of the user", paramType="query", required="true", allowMultiple="false", dataType="string")
 *          )
 *      )
 *  )
 * )
 * 
 * <p>API Login Action</p>
 * <p>Handles login requests</p>
 */
class Action_login extends AbstractAPIAction implements NoSecuredAction {

    /**
     * <p>Default method for this action</p>
     * <p>Executes the login check method</p>
     * @param Request The current request
     * @param Response The Response object to be sent and where to put the response of this action
     */
    public function index($request, $response) {
        $user = $request->getParam('user');
        $pass = $request->getParam('pass');

        if ($user == null || $pass == null) {
            $this->createErrorResponse('Bad parameters. user or pass parameters are missing');
            return;
        }

        $authenticator = new Authenticator();
        $success = $authenticator->login($user, $pass);

        if ($success) {
            $responseContent = array('ximtoken' => $this->generateXimToken($user));
            $this->responseBuilder->ok()->content($responseContent)->build();
        } else {
            $this->createErrorResponse('Incorrect login parameters');
        }

        return;
    }

    /**
     * <p>Generates a Ximdex token to be used in subsequently requests to the Ximdex API</p>
     * @param string $user the user for which to generate the token
     * @return string the generated token
     */
    private function generateXimToken($user) {
        $tokenService = new TokenService();
        $token = $tokenService->getToken($user, Config::getValue('TokenTTL'));
        return $token;
    }

}
