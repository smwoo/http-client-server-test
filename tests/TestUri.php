<?php

require __DIR__ . '/../vendor/autoload.php';

use \pillr\library\http\Uri;

class TestUri extends \PHPUnit_Framework_TestCase {
    public function testConstructAndGet(){
        $uri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");

        $this->assertEquals(
            $uri->getScheme(),
            "http"
        );

        $this->assertEquals(
            $uri->getAuthority(),
            "userinfo:password@myanimelist.net:8080"
        );


        $this->assertEquals(
            $uri->getUserInfo(),
            "userinfo:password"
        );

        $this->assertEquals(
            $uri->getHost(),
            "myanimelist.net"
        );

        $this->assertEquals(
            $uri->getPort(),
            8080
        );

        $this->assertEquals(
            $uri->getPath(),
            "/search/all"
        );

        $this->assertEquals(
            $uri->getQuery(),
            "q=one%20piece"
        );

        $this->assertEquals(
            $uri->getFragment(),
            "extrafragment"
        );
    }

    public function testWithScheme(){
        $uri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");

        $withUri = $uri->withScheme("https");

        $this->assertEquals(
            $withUri->getScheme(),
            "https"
        );

        $this->assertFalse(
            $uri->getScheme() == "https"
        );
    }

    public function testFailInvalidSchemeWithScheme(){
        $this->expectException(InvalidArgumentException::class);

        $uri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");
        $withUri = $uri->withScheme("mailto");
    }

    public function testFailInvalidSchemeColonWithScheme(){
        $this->expectException(InvalidArgumentException::class);

        $uri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");
        $withUri = $uri->withScheme("https:");
    }

    public function testWithUserInfo(){
        $uri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");

        $withUri = $uri->withUserInfo("michael", "password");

        $this->assertEquals(
            $withUri->getUserinfo(),
            "michael:password"
        );

        $this->assertEquals(
            $withUri->getAuthority(),
            "michael:password@myanimelist.net:8080"
        );

        $this->assertFalse(
            $uri->getUserinfo() == "michael:password"
        );

        $this->assertFalse(
            $uri->getAuthority() == "michael:password@myanimelist.net:8080"
        );
    }

    public function testWithNoPasswordUserInfo(){
        $uri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");

        $withUri = $uri->withUserInfo("michael");

        $this->assertEquals(
            $withUri->getUserinfo(),
            "michael"
        );

        $this->assertEquals(
            $withUri->getAuthority(),
            "michael@myanimelist.net:8080"
        );

        $this->assertFalse(
            $uri->getUserinfo() == "michael"
        );

        $this->assertFalse(
            $uri->getAuthority() == "michael@myanimelist.net:8080"
        );
    }

    public function testWithEmptyUserInfo(){
        $uri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");

        $withUri = $uri->withUserInfo("", "password");

        $this->assertEquals(
            $withUri->getUserinfo(),
            ""
        );

        $this->assertEquals(
            $withUri->getAuthority(),
            "myanimelist.net:8080"
        );

        $this->assertFalse(
            $uri->getUserinfo() == ""
        );

        $this->assertFalse(
            $uri->getAuthority() == "myanimelist.net:8080"
        );
    }

    public function testWithHost(){
        $uri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");

        $withUri = $uri->withHost("example.com");

        $this->assertEquals(
            $withUri->getHost(),
            "example.com"
        );

        $this->assertEquals(
            $withUri->getAuthority(),
            "userinfo:password@example.com:8080"
        );

        $this->assertFalse(
            $uri->getHost() == "example.com"
        );

        $this->assertFalse(
            $uri->getAuthority() == "userinfo:password@example.com:8080"
        );
    }

    public function testFailInvalidHostSlashWithHost(){
        $this->expectException(InvalidArgumentException::class);

        $uri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");
        $withUri = $uri->withHost("invalid/Host");
    }

    public function testFailInvalidHostSqBracketWithHost(){
        $this->expectException(InvalidArgumentException::class);

        $uri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");
        $withUri = $uri->withHost("invalid][Host");
    }

    public function testWithPort(){
        $uri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");

        $withUri = $uri->withPort(88);

        $this->assertEquals(
            $withUri->getPort(),
            88
        );

        $this->assertEquals(
            $withUri->getAuthority(),
            "userinfo:password@myanimelist.net:88"
        );

        $this->assertFalse(
            $uri->getPort() == 88
        );

        $this->assertFalse(
            $uri->getAuthority() == "userinfo:password@myanimelist.net:88"
        );

        $withUri = $uri->withPort(80);

        $this->assertEquals(
            $withUri->getPort(),
            null
        );
    }

    public function testFailInvalidPortRangeWithPort(){
        $this->expectException(InvalidArgumentException::class);

        $uri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");
        $withUri = $uri->withPort(-100);
    }

    public function testWithPath(){
        $uri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");

        $withUri = $uri->withPath("/browse/airing");

        $this->assertEquals(
            $withUri->getPath(),
            "/browse/airing"
        );

        $this->assertFalse(
            $uri->getPath() == "/browse/airing"
        );
    }

    public function testFailNoSlashhWithAuthorityWithPath(){
        $this->expectException(InvalidArgumentException::class);

        $uri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");
        $withUri = $uri->withPath("search/all");
    }

    public function testFailInvalidSlashWithoutAuthorityWithPath(){
        $this->expectException(InvalidArgumentException::class);

        $uri = new Uri("https:path/path");
        $withUri = $uri->withPath("//search");
    }

    public function testWithQuery(){
        $uri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");

        $withUri = $uri->withQuery("new-query");

        $this->assertEquals(
            $withUri->getQuery(),
            "new-query"
        );

        $this->assertFalse(
            $uri->getQuery() == "new-query"
        );
    }

    public function testFailHashtagWithQuery(){
        $this->expectException(InvalidArgumentException::class);

        $uri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");
        $withUri = $uri->withQuery("new#query");
    }

    public function testWithFragment(){
        $uri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");

        $withUri = $uri->withFragment("newFragment");

        $this->assertEquals(
            $withUri->getFragment(),
            "newFragment"
        );

        $this->assertFalse(
            $uri->getFragment() == "newFragment"
        );
    }

    public function testToString(){
        $uri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");

        $this->assertEquals(
            (string)$uri,
            "http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment"
        );
    }
} ?>