if( match (null
    , getResponse("<?= $responseIdentifier ?>")
    , getCorrect("<?= $responseIdentifier ?>")) ) 
{
    setOutcomeValue("<?= $outcomeIdentifier ?>"
        , sum (null
            , getVariable ("<?= $outcomeIdentifier ?>")
            , 1)
    );
} 
else 
{
    setOutcomeValue("<?= $outcomeIdentifier ?>"
        , sum (null
            , getVariable ("<?= $outcomeIdentifier ?>")
            , 0)
    );
}
