<?php


    include_once('_template/_header.php');

    if(!_getUsername())
        Header('Location: index.php');

    $SA         = null;
    $RealmID    = null;
    $GUID       = null;
    $REALSON    = null;

    if(isset($_GET['realmid']) && isset($_GET['guid'])) {
        unset($_SESSION['TCA']);
        $RealmID    = (int)$_GET['realmid'];
        $GUID       = (int)$_GET['guid'];
        if(!is_numeric($RealmID) || !is_numeric($GUID))
            Header('Location: _userside.php');
    } else if(isset($_SESSION['TCA'])) {
        $SA         = $_SESSION['TCA'];
        $RealmID    = $_SESSION['TCA']['RealmID'];
        $RealmName  = $_SESSION['TCA']['RealmName'];
        $GUID       = $_SESSION['TCA']['CharGUID'];
        $CharName   = $_SESSION['TCA']['CharName'];

        $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);
        if(_isEnoughMythCoins($PriceForRemoveDeserter, $connection)) {
            _SpendMythCoins($PriceForRemoveDeserter, 8, $CharName, $GUID, $RealmName, $RealmID, "test tip, will be used next time", $connection);
            mysql_close($connection) or die(mysql_error());
            $REALSON = _GDiv($L[17]);
            _removeDeserterDebuffFromCharacter($GUID, $RealmID, $DBUser, $DBPassword);
        } else {
            mysql_close($connection) or die(mysql_error());
            $REALSON = _getNotEnoughtFireSTR();
        }
        unset($_SESSION['TCA']);
    } else Header('Location: _userside.php'); // die("EXEPTION");

    if(_doesRealmExists($RealmID, $DBUser, $DBPassword, isset($SA))) {
        if(_doesCharacterExistsOnAccount($DBUser, $DBPassword, $RealmID, $GUID, isset($SA))) {
            if(_doesCharacterNotOnlineATM($DBUser, $DBPassword, $RealmID, $GUID)) {
                if(!_doesCharacterHasDeserterDebuff($GUID, $RealmID, $DBUser, $DBPassword))
                    echo _getAlreadyEffectSTR($L[144]);
                else
                    _FORM_TO_CHAR_ACTIONS($SA ? $SA : _FORM_CHAR_ARRAY($AccountDBHost, $AccountDB, $DBUser, $DBPassword, $RealmID, $GUID) /* CHECK FOR SESSION ARRAY */,
                                        $L[90], $REALSON, $PriceForRemoveDeserter,
                                         null /* NEW LEVEL */,
                                         null /* NEW NAME */,
                                         null /* NEW RACE */,
                                         null /* NEW CLASS */,
                                         null /* CUSTOMIZE */);
            } else echo _RDiv($L[60]);
        } else echo _RDiv($L[9]);
    } else echo _RDiv($L[9]);

    include_once('_template/_footer.php');
    ob_end_flush();
?>