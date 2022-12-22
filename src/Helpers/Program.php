<?php


namespace Vzr\Helpers;


class Program
{

    /**
     * @param $programs
     * @param $programId
     * @return array
     */
    public static function getProgramByProgramId($programs, $programId)
    {
        return array_filter($programs, function ($program) use ($programId) {
            return $program['ProgId'] == $programId;
        });
    }

    public static function getParentContract($contracts, $policyNumber){
        $parentContract = array_filter($contracts, function($contract)  use ($policyNumber){
            return $contract['PolicyNumber'] == $policyNumber;
        });
        return reset($parentContract);
    }
}
