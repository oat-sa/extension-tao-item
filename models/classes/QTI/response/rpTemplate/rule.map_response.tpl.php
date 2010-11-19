if( isNull(null
    , getResponse("<?= $responseIdentifier ?>")) ) 
{
    setOutcomeValue("<?= $outcomeIdentifier ?>"
        , sum (null
            , getVariable ("<?= $outcomeIdentifier ?>")
            , 0)
    );
} 
else 
{ 
    setOutcomeValue("<?= $outcomeIdentifier ?>"
        , sum (null
            , getVariable ("<?= $outcomeIdentifier ?>")
            , mapResponse(null
                , getMap("<?= $responseIdentifier ?>")
                , getResponse("<?= $responseIdentifier ?>"))
        )
    );
}
