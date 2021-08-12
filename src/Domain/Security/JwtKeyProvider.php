<?php

declare(strict_types=1);

namespace GimmeBook\Domain\Security;

class JwtKeyProvider
{
    public function getPrivateKey(): string
    {
        return <<<KEY
-----BEGIN RSA PRIVATE KEY-----
MIIEowIBAAKCAQEAtxln4mrhIet+mlcFNX6F9+SgfTI2WROdxoqjNzvA7pdazNlU
DvEoRu4OF4Qp0fkZWNoucUQaZVKp7NEbwfEzvRUw4/dR3m6YK2Sq7Z3zsH7tUmKt
0Vamcs8UCGc2iFVogzzK/Uc/3gXhuIJt0c/jHTnaE+zY6gvY7AvVSExzLzCKHI2t
Zi2K2DVnCMNVge8m8/nUDdKbp1T2/vRWS+vVWs9ra6nuORd8YUhPiqQFwzE1Z6Wx
WKrAD24GWegYZpDnW+JO5z1BG+B7MUQRhN8/NdA6WesoncUoyMihuiEnvtvTK1Oc
PRD3yeC76d+2brmbC7AjwLMrGP4rUbQrQjQbswIDAQABAoIBAHFkp4R+YnzKRja5
S72MZNVX3tiKH0RdNKn/tAMB24MncxFISpmSWjpLNaj0rZ2fIkZ3oKl+3sX/QsMp
4YHjqgIgvI2B3k7duRmul3jfCEs24CzUdgceHakee66UlR4rnrUgEip3VKNgiyDk
jbSRhXrVGCyc0t1nhujwjx7eUbtXy1gexroydM76hU3Lz3+josxtqOhM1xrtqvR5
/pf/+piqDr8IbOmkgUzAyV9ljaR3Xir2zwqK6yBQOvO2tWamD4dOFdttV5o+v0FP
o/NHWiNgYjWILZ0Q7TjgHTkVfufScjPlMHjLpNxshTKfGHy9d3M5oJ5GQ0iaYYRs
w6QuKFkCgYEA8qa5pVHAcNAyihiR7bIVFAQPaHiBPW4epWxcAusWV6gZ9OvJSK8F
d6drz9PlEeSWTUfQ18oIUqtwbqNbSyQS7pCVo+oooehbSTc7P8jHUVUad8PBTZCC
7goF9oBYu59kknIF6QTClqIe1oaPo0U0bS9uqMuX4KTbIOvQAD/8nl0CgYEAwSwB
Vr8PZfn2Ypjg3srn7bxJU37TvYFOwSAFTh/LWefWrN57UWdrxg9brMq9r0A00ro4
ALLr4sVC/EKj7jHmjespKY6Fw7e1mgYZ6saZicLyvjdPTu41yHfedxF5TUpHTBZz
BNT9D1QXCUbAOG11O5S8OVq5+4K3KerhwHhWYU8CgYEA2e7ue9GyAmCrk6Y/onm4
PrLq18yrXu4BblelSCW2emILdhMzRCmVwoLG7PEGIwzoBV7D7puQ4BcEMwpa22D5
8/Q9wet0NP6IxnhpqX79rUm/LOPPQIfTYFH2Sw+5IkIlRPZN4pwY3Muc4NYYOe7V
CF96hvXcYbIO8UF6Hk9Z5y0CgYA+5O8CqW6AC2S3MYN1xqbA9t8A+nhCaUmVA7H2
f8+b1CpHWqDYHk4uzG1S0yfzWXpZahw1zguTaBqpO6FYOpMQfhKG30qaMMRGA9qI
YGU5P2n9mNCPqGuGe9DI/7149shD88M7PYWvafeeI5UOSkUzQvgNzIZlZ2fvk/Qw
0H1/bQKBgF7jCjueq43OAMo5ihdB5vk0UWr5l9Hgx7WmlahEC5bNR4D56B/vlM4E
jFIVRTGovDtWRn1wW0xOubBdF+6fC7KRo9jKMPyjmorOaswnvqkd6o3SXkU9EpR+
Cs/R8wEM2Gbeyz7Wfp8EiAveK5/EI7ZZOdOmbFp2w6KOFglwp4dE
-----END RSA PRIVATE KEY-----
KEY;
    }

    public function getPublicKey(): string
    {
        return <<<KEY
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtxln4mrhIet+mlcFNX6F
9+SgfTI2WROdxoqjNzvA7pdazNlUDvEoRu4OF4Qp0fkZWNoucUQaZVKp7NEbwfEz
vRUw4/dR3m6YK2Sq7Z3zsH7tUmKt0Vamcs8UCGc2iFVogzzK/Uc/3gXhuIJt0c/j
HTnaE+zY6gvY7AvVSExzLzCKHI2tZi2K2DVnCMNVge8m8/nUDdKbp1T2/vRWS+vV
Ws9ra6nuORd8YUhPiqQFwzE1Z6WxWKrAD24GWegYZpDnW+JO5z1BG+B7MUQRhN8/
NdA6WesoncUoyMihuiEnvtvTK1OcPRD3yeC76d+2brmbC7AjwLMrGP4rUbQrQjQb
swIDAQAB
-----END PUBLIC KEY-----
KEY;
    }
}