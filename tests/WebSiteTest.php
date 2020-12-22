<?php

namespace App\Tests;


use App\Controller\WebSiteController;
use PHPUnit\Framework\TestCase;

class WebSiteTest extends TestCase{

    public function testAdd(){
       /* $web_site = new WebSiteController();
        $result = $web_site->add(1,5);
        $this->assertEquals(6, $result);*/
       //$this->assertIsArray();
    }

/*    public function testGetAllStudents(){
        $s=new Students;
        $students=$s->getStudents("","");
        $this->assertIsArray($students);
        $this->assertEquals(7,count($students));
        $first=$students[0];    //Previous assert tells us this is safe
        $this->assertInstanceOf('Student',$first);
    }

    public function testGetOnlyStudentNamedBob(){
        $s=new Students;
        $students=$s->getStudents("Bob","");
        $this->assertIsArray($students);
        $this->assertEquals(1,count($students));
        $first=$students[0];    //Previous assert tells us this is safe
        $this->assertInstanceOf('Student',$first);
        $this->assertEquals('Bob',$first->getStudentName());
    }*/


}

