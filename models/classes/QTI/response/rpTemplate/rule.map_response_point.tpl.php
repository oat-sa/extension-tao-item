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
            , mapResponsePoint(null
                , getMap("<?= $responseIdentifier ?>", "area")
                , getResponse("<?= $responseIdentifier ?>"))
        )
    );
}
