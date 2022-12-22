<?php


namespace Vzr\Helpers;


class Mock
{
    public static function registerOperation()
    {
        return array(
            'RegisterOperationResponse' => array(
                'RegisterOperationInfo' => array(
                    'PolicyId' => '123456',
                    'EmployeePolicyId' => '123456',
                    'GLOption' => '1',
                    'PolicyNumber' => '123321',
                    'AgNumber' => 'ASDASDASD',
                    'InsurProg' => 'ДМС для бедных',
                    'StartDate' => '29.08.2022',
                    'EndDate' => '29.08.2023',
                    'CancelDate' => '',
                    'InsurerFName' => 'МРЦ',
                    'ChangeProg' => 'N',
                    'GLRelatives' => 0,
                    'modelVzr' => '1'
                )
            )
        );
    }

    public static function getMessApp()
    {
        return array(
            'getMessAppResponse' => array(
                'MessAppList' => array(
                    'Mess01' => 'Информационное сообщение для экрана «Бонусный полис ВЗР»',
                    'Mess02' => 'Информационное сообщение для экрана «Заполнить форму»',
                    'Mess03' => 'Информационное сообщение для экрана «Черновик полиса ВЗР»'
                )
            )
        );
    }

    public static function getFullVzrInfo()
    {
        return array(
            'getFullVzrInfoResponse' => array(
                'FullVzrInfo' => array(
                    array(
                        'territory' => 'Весь мир',
                        'policyId' => '123456',
                        'policyNumber' => '123321',
                        'fio' => 'Хасанов Максим Ривхатович',
                        'birthDate' => '29.08.1984',
                        'startDate' => '29.08.2022',
                        'expirationDate' => '29.08.2023',
                        'MedProgInfo' => array(
                            array(
                                'medProgId' => '12345',
                                'costRur' => '1000.00',
                                'costVal' => '100',
                                'curId' => 'USD'
                            ),
                            array(
                                'medProgId' => '123456',
                                'costRur' => '1000.00',
                                'costVal' => '100',
                                'curId' => 'EUR'
                            ),
                            array(
                                'medProgId' => '1234567',
                                'costRur' => '1000.00',
                                'costVal' => '100',
                                'curId' => 'RUB'
                            )
                        ),
                        'TIPolicies' => array(
                            'PolicyNumber' => '12345',
                            'PolicyLink' => 'https://www.Vzr.ru/upload/iblock/b9c/b9cd44ec8e00baf172495f4b7626d2f8.pdf',
                            'Status' => 'Active'
                        )
                    ),
                    array(
                        'territory' => 'СНГ',
                        'policyId' => '1234567',
                        'policyNumber' => '123321',
                        'fio' => 'Хасанов Максим Второй',
                        'birthDate' => '29.08.1984',
                        'startDate' => '29.08.2022',
                        'expirationDate' => '29.08.2023',
                        'MedProgInfo' => array(
                            array(
                                'medProgId' => '12345',
                                'costRur' => '1000.00',
                                'costVal' => '100',
                                'curId' => 'USD'
                            ),
                            array(
                                'medProgId' => '123456',
                                'costRur' => '1000.00',
                                'costVal' => '100',
                                'curId' => 'EUR'
                            ),
                            array(
                                'medProgId' => '1234567',
                                'costRur' => '1000.00',
                                'costVal' => '100',
                                'curId' => 'RUB'
                            )
                        ),
                        'TIPolicies' => array(
                            'PolicyNumber' => '12345',
                            'PolicyLink' => 'https://www.Vzr.ru/upload/iblock/b9c/b9cd44ec8e00baf172495f4b7626d2f8.pdf',
                            'Status' => 'Draft'
                        )
                    ),
                    array(
                        'territory' => 'Весь мир',
                        'policyId' => '12345678',
                        'policyNumber' => '123321',
                        'fio' => 'Хасанов Максим Третий',
                        'birthDate' => '29.08.1984',
                        'startDate' => '29.08.2022',
                        'expirationDate' => '29.08.2023',
                        'MedProgInfo' => array(
                            array(
                                'medProgId' => '12345',
                                'costRur' => '1000.00',
                                'costVal' => '100',
                                'curId' => 'USD'
                            ),
                            array(
                                'medProgId' => '123456',
                                'costRur' => '1000.00',
                                'costVal' => '100',
                                'curId' => 'EUR'
                            ),
                            array(
                                'medProgId' => '1234567',
                                'costRur' => '1000.00',
                                'costVal' => '100',
                                'curId' => 'RUB'
                            )
                        )
                    ),
                )
            )
        );
    }

    public static function getShortVzrInfo()
    {
        return array(
            'getShortVzrInfoResponse' => array(
                'ShortVzrInfo' => array(
                    'policyId' => '123456',
                    'fio' => 'Хасанов Максим Ривхатович',
                    'birthDate' => '29.08.1984',
                    'expirationDate' => '29.08.2023',
                    'TIPolicies' => array(
                        'PolicyNumber' => '12345',
                        'PolicyLink' => 'https://b2b.Vzr.ru/cxf/police-new',
                        'Status' => 'Draft'
                    )
                )
            )
        );
    }

    public static function createFullVzrPolicy()
    {
        return array(
            'createFullVzrPolicyResponse' => array(
                'successes' => 0,
                'message' => 'Вы восхитительны!',
                'PolicyId' => '12312313213',
                'TIPolicy' => array(
                    'PolicyNumber' => '1231231321321',
                    'PolicyLink' => 'https://b2b.Vzr.ru/cxf/police-new',
                    'Status' => 1
                )

            )
        );
    }
}