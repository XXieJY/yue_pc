<?php
class LechuangAction extends Action {

    private $cp_id = 62;
    private $cp_name='乐创';

    //这边测试需要修改
       function __construct() {
         parent::__construct();
         $this->cpbook=M("CpBook");    //需要修改
         $this->book=M('Book');        //需要修改
         $this->book_content=M('book_content');//需要修改
         $this->book_contents=M('BookContents');//需要修改
       }

    //作品查看
    public function index($cp) {
        $where['cp_id'] = $cp;
        $where['web_id'] = 4;
        //数据处理
        $bbb = M('Book');
        import('ORG.Util.Page'); // 导入分页类       
        $count = $bbb->where($where)->count(); // 查询满足要求的总记录数
        $Page = new Page($count, 15); // 实例化分页类 传入总记录数和每页显示的记录数               
        $book = $bbb->where($where)->field('fu_book,book_name,author_name,cp_name,words,new_time,time,state')->order('time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('book', $book);
        $Page->setConfig('theme', "<div class=\"pageSelect\"> <span>共 <b>%totalRow%</b> 条 当前 <b>%nowPage%</b>/%totalPage% 页</span><div class=\"pageWrap\">%prePage%%upPage%%linkPage%%downPage%%nextPage%</div></div>");
        $show = $Page->show(); // 分页显示输出
        $this->assign('page', $show); // 赋值分页输出
        $this->display('Interface@Lechuang');
    }

    public function  test(){
      // header('Content-type: text/html;charset=utf-8');
          $bookid = 1000006428;
         $chapterid = 213;
        // print_r($this->booklistapi());
        // echo "<hr>";
        // print_r($this->getbookinfo($bookid));
        // echo "<hr>";
        // print_r($this->getchapters($bookid));
        // echo "<hr>";
      //   //print_r($this->update(2964));
         print_r($this->getcontent($bookid,$chapterid));
      exit();
    }
        //书单
       public function  booklistapi(){
        header("Content-type: text/html; charset=utf-8");
         $booklists = file_get_contents("http://www.leread.com/ilereader/push/book/list?userName=yueming02&sign=ad674121baea9bcc15928966e3693bed");
         $booklists=preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $booklists);
         $booklists = $this->ext_json_decode($booklists);

         return $booklists->data;
       }
       //处理json返回数组
       public function ext_json_decode($str, $mode=false){
       header('Content-type: text/html;charset=utf-8');  
            $str = preg_replace('/([{,])(\s*)([A-Za-z0-9_\-]+?)\s*:/','$1"$3":',$str);
            //$str = str_replace('\'','"',$str);
            $str = str_replace(" ", "", $str);
            $str = str_replace('\t', "", $str);
            $str = str_replace('\r', "", $str);
            $str = str_replace("\l", "", $str);
            // $str = preg_replace('/s+/', '',$str); 
            $str = trim($str,chr(239).chr(187).chr(191));
            
            return json_decode($str, $mode);  
        }

      //获取第三方书籍详情
      public function  getbookinfo($partnerbookid){
        header("Content-type: text/html; charset=utf-8");
        $bookinfo = file_get_contents("http://www.leread.com/ilereader/push/book/detail?userName=yueming02&sign=ad674121baea9bcc15928966e3693bed&bookId=".$partnerbookid);
        $bookinfo=preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $bookinfo);
        $bookinfo = $this->ext_json_decode($bookinfo);
       
        return   $bookinfo->data;
      }
        //需要重写
      private  function  getchapters($partnerbookid){
        header("Content-type: text/html; charset=utf-8");
        $chapters = file_get_contents("http://www.leread.com/ilereader/push/book/chapters?userName=yueming02&sign=ad674121baea9bcc15928966e3693bed&bookId=".$partnerbookid);
        $chapters=preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $chapters);
        $chapters = $this->ext_json_decode($chapters);

        return   $chapters->data;
      }
        //需要重写
       private  function  getcontent($partner_bookid,$partner_chapterid){
            header("Content-type: text/html; charset=utf-8");
            $content = file_get_contents("http://www.leread.com/ilereader/push/book/content?userName=yueming02&sign=ad674121baea9bcc15928966e3693bed&bookId=".$partner_bookid."&sequence=".$partner_chapterid);
           // $content=preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $content);
            // $content = preg_replace('/s+/', '',$content); 
            $contents = $this->ext_json_decode($content);

            return $contents->data;
      }

     //huo书籍列表
      public function booklist(){
          $booklist=$this->booklistapi();
          //print_r($booklist);//die();
          foreach ($booklist as $key => $value) {
            if($key<1000){
              $cpbookid=$value->bookId;   //需要修改
               //echo $cpbookid;exit;
              $bookinfo=$this->getbookinfo($cpbookid);
              //print_r($bookinfo);exit;   
              $bookname=(string)$bookinfo->bookName;   //需要修改
              $authorname=(string)$bookinfo->author;  //需要修改
              $bookpic=(string)$bookinfo->coverPath;//需要修改
              $gender=1;         //1男2女                  //需要修改
              $state=(int)$bookinfo->serialProp==1?1:2;;   //1 连载 2完本
              $keywords="";    //需要修改
              $description=(string)$bookinfo->introduce;//需要修改
              $type = (int)$this->gettype($bookinfo->bookType);
              // echo $bookname;
              // echo "<br>";
              // echo $authorname;
              // echo "<br>";
              // echo $bookpic;
              // echo "<br>";
              // echo $gender;
              // echo "<br>";
              // echo $state;
              // echo "<br>";
              // echo $keywords;
              // echo "<br>";
              // echo $description;
              // echo "<hr>";
              // echo $type;
              // echo "<hr>";
              // die();
             if(!$this->is_cpcreaete($cpbookid)) {
                   $rid=$this->createcpbook($cpbookid,$bookname,0);
                   if($rid>0&&!$this->is_create($bookname)){
                        //创建主库书籍
                        $rbookid=$this->createbook($bookname,$authorname,$bookpic,$gender,$state,$keywords,$description,$type);
                        if($rbookid){
                          //去更新采集表 书籍id
                                $this->updatecpbook($cpbookid,$rbookid);
                                echo  $bookname."&nbsp;&nbsp;创建成功<br>";
                        }
                   }elseif($rid>0&&$b=$this->is_create($bookname)){
                                //更新已创建书籍
                                $this->updatecpbook($cpbookid,$b);
                                //更新章节数量
                                $this->updatechapternum($rid,$b);
                                $this->updateapinum($rid,$cpbookid);
                                //更新图书信息
                                //$this->updatebook($cpbookid,$bookpic);
                   }
             }else{
                 //更新图书信息
                 //echo $cpbookid;echo $bookpic;//exit;
                 //$this->updatebook($cpbookid,$bookpic);
                echo $bookname."&nbsp;&nbsp;已采集<br>"; 
             } 
             }
            }
            echo "<p style='color:red'>采集完毕</p>";
      }

        //检测采集书单里面是否有这本书
      private function is_cpcreaete($partnerbookid){
            $mycpbook = $this->cpbook->where(array('cp_id' => $this->cp_id, 'fubook_id' =>$partnerbookid))->find();
            if(is_array($mycpbook)){
                return    $mycpbook['id'];
            }else{
                return   0;
            }
      }

        //入库规则
      private  function createcpbook($partnerbookid,$partnerbookname,$type){
            $time=date('Y-m-d H:i:s',time());
            $data['fubook_id'] = (int)$partnerbookid;
                $data['cp_id'] = $this->cp_id;
                $data['type'] = $type;
                $data['fubook_name'] = $partnerbookname;
                $data['fu_num'] = 0;
                $data['fu_time'] =$time;
                $data['book_id'] = 0;
                $data['num'] = 0;
                $data['book_time']=$time;
                $data['time'] = $time;
                $rid=$this->cpbook->add($data);
            if($rid){
              $this->updateapinum($rid,$partnerbookid);
              return  $rid;
            }else{
              echo  "创建cp书籍出错";
            }
            
      }

       //判断这本书是不是新书
      private  function is_create($bookname){

        $isbook=$this->book->where(array('fu_web'=>0,'cp_id'=>$this->cp_id,'book_name'=>$bookname))->find();
        if(is_array($isbook)){
         return    $isbook['book_id'];
        }else{
          return   0;
        }
      }

        private function createbook($bookname,$author_name,$coverUrl,$gender,$state,$keywords,$book_brief,$type)
       {
            $time=date('Y-m-d H:i:s',time());
            $data['web_id']=4;
            $data['cp_id']=$this->cp_id;
            $data['cp_name']=$this->cp_name;
            $data['book_name']=$bookname;
            $data['author_name']=$author_name;
            $data['type_id']=$type;//这边需要做映射
            $data['gender']=$gender;
            $imgname=time().rand(1,999999);
            // $imgname=time();
            $r=$this->getImage($coverUrl,'./Upload/Book/da',$imgname.'.jpg',$type=0);
            $r=$this->getImage($coverUrl,'./Upload/Book/zhong',$imgname.'.jpg',$type=0);
            $r=$this->getImage($coverUrl,'./Upload/Book/xiao',$imgname.'.jpg',$type=0);
            $data['upload_img']=$r['file_name'];
            $data['state']=$state;
            $data['vip']=0;
            $data['signing']="SS";
            $data['keywords']=$keywords;
            $data['book_brief']=$book_brief;
            $data['audit']=2;
            $data['time']= $time;
            $data['new_time']= $time;
            $data['is_show']=1;
            $rid=$this->book->add($data);
            $bang = M('BookStatistical'); //作品榜单
            $bangdan = $bang->add(array('book_id' => $rid));
            $isok =$this->book->where(array('book_id' => $rid))->save(array('fu_book' =>$rid));
            //api里面的章节
            return  $rid;
       }

       //更新图书信息
       private function updatebook($cpbookid,$coverUrl)
       {
            $where[fubook_id] = $cpbookid;
            $cpbookinfo = M('CpBook')->where($where)->find();
            $fubook_id =$cpbookinfo[book_id]; 
            $imgname=time().rand(1,999);
            $r=$this->getImage($coverUrl,'./Upload/Book/da',$imgname.'.jpg',$type=0);
            $r=$this->getImage($coverUrl,'./Upload/Book/zhong',$imgname.'.jpg',$type=0);
            $r=$this->getImage($coverUrl,'./Upload/Book/xiao',$imgname.'.jpg',$type=0);
            $data['upload_img']=$r['file_name'];
            $data['fu_book']=$fubook_id;

            $isok =M('Book')->where(array('fu_book' => $fubook_id))->save($data);
       }

      private  function  updatecpbook($partnerbookid,$rbookid){ 
           $this->cpbook->where(array('cp_id'=>$this->cp_id,'fubook_id'=>$partnerbookid))->save(array('book_id'=>$rbookid));
      }

      public  function  updatechapternum($id,$bookid){
            $num=$this->getchapternums($bookid);
            $cpbook = $this->cpbook->where(array('id' => $id, 'cp_id' =>$this->cp_id))->save(array('num'=>$num));  
      }

      public  function  updateapinum($id,$partner_bookid){
            $num=count($this->getchapters($partner_bookid));
            $cpbook = $this->cpbook->where(array('id' => $id, 'cp_id' =>$this->cp_id))->save(array('fu_num'=>$num));  
      }

      public   function  up(){
          $mycpbook = $this->cpbook->where(array('cp_id' => $this->cp_id))->order('id desc')->select();
        //  echo  $this->cpbook->getLastSql();
          $this->assign('mycpbook', $mycpbook);
          $this->display();
      }

       private  function  getchapternums($bookid){
        return    $this->book_content->where(array('fu_book'=>$bookid))->count();
      }
//跟新
       public  function  update($id){
            $cpbook= $this->cpbook->where(array('id' => $id,'cp_id'=>$this->cp_id))->find();
            //先检测本书有没有章节
            $havecount=$this->getchapternums($cpbook['book_id']);
            $apicount=$this->getchapters($cpbook['fubook_id']); 
            //print_r($apicount);die;
            if($havecount==count($apicount)){
              echo  $cpbook[fubook_name]."&nbsp;&nbsp;没有需要更新的内容</br>";
              //exit;
            }elseif($havecount<count($apicount)){
               for($i=$havecount;$i<count($apicount);$i++){
                    $chapterid=$apicount[$i]->sequence;          //需要修改
                    $cpbookid=$cpbook['book_id'];            //需要修改
                    $title=$apicount[$i]->name;//需要修改
                    $content=$this->getcontent($cpbook['fubook_id'],$chapterid);
                    $wordnum=$this->trimall($content);
                    $price=$this->price($wordnum);
                    // echo "章节id:".$chapterid;
                    // echo "<br>";
                    // echo "书本id:".$cpbookid;
                    // echo "<br>";
                    // echo "章节名:".$title;
                    // echo "<br>";
                    // echo "章节字数:".$wordnum;
                    // echo "<br>";
                    // echo "章节钱数:".$price;
                    // echo "<br>";
                    // echo "章节内容:".$content;
                    // echo "<hr>";
                    // exit;
                    $this->insertcon($cpbookid,$title,$wordnum,$price,$content,$id);
               }
                $this->updateapinum($id,$cpbook['fubook_id']);
            $this->updatechapternum($id,$cpbook['book_id']);
            echo $cpbook[fubook_name]."&nbsp;&nbsp;更新结束</br>";
            }
           
      }

      //章节入库规则
      private  function insertcon($bookid,$title,$numbers,$price,$content,$id){
          $time=$time=date('Y-m-d H:i:s',time());
          $data['fu_book'] = $bookid;
          //获取当前最大章节
          //$num=$this->book_content->where(array('fu_book'=>$bookid))->max('num');
          $num=$this->book_content->where(array('fu_book'=>$bookid))->count();
          $data['num'] = $num+1;
          $data['title'] =$title;
          $data['number'] = $numbers;
          $price=$num>20?$price:0;
          $data['the_price'] =$price;
          $data['caudit'] = 2;
          $data['attribute'] = $time;
          $data['time'] = $time;
          $iscon = $this->book_content->add($data);
          if($iscon){
            $iscons = $this->book_contents->add(array('content_id' => $iscon, 'content' =>$content));
          }
           if ($iscon && $iscons) {
                $this->book->where(array('book_id' => $bookid))->save(array('chapter' => array('exp', "chapter+1"), 'words' => array('exp', "words+$numbers")));
                $re = $this->book->where(array('book_id' => $bookid))->find();
                $chapter = $re['chapter'];
                $words = $re['words'];
                $this->assign_book_update($chapter,$words,$bookid);
              //  $this->cpbook->where(array('id' => $id))->save(array('fu_num' => array('exp', "fu_num+1")));
           } else {
            //    $cpboks->where(array('id' => $id))->save(array('termination' => 1));
                 echo $id . "<font color=red>章节更新出错:请技术进行检查</font></br>";
                exit();
           }
      }

      //连载授权书章节字数更新
      public function assign_book_update($chapter,$words,$bookid){
          $data=$this->book->where(array('fu_book'=>$bookid))->select();
          foreach($data as $k=>$v){
             $this->book->where(array('book_id' => $v['book_id']))->save(array('chapter' => $chapter, 'words' => $words));
          }
      }

     public function  price($number){
       return  floor($number/1000*6);
     }

  //字数统计函数
    private  function trimall($str) {//删除空格
        $qian = array(" ", "　", "\t", "\n", "\r");
        $hou = array("", "", "", "", "");
        $str = str_replace($qian, $hou, $str);
        $str = mb_convert_encoding($str, 'GBK', 'UTF-8');
        preg_match_all("/[" . chr(0xa1) . "-" . chr(0xff) . "]{2}/", $str, $m);
        $mu = count($m[0]);
        unset($str);
        unset($m);
        return $mu;
    }

  /* 
     *功能：php完美实现下载远程图片保存到本地 
     *参数：文件url,保存文件目录,保存文件名称，使用的下载方式 
     *当保存文件名称为空时则使用远程文件原来的名称 
  */ 
  private function getImage($url,$save_dir='',$filename='',$type=0){ 
     if(trim($url)==''){ 
        return array('file_name'=>'','save_path'=>'','error'=>1); 
     } 
     if(trim($save_dir)==''){ 
        $save_dir='./'; 
     } 
     if(trim($filename)==''){//保存文件名 
        $ext=strrchr($url,'.'); 
        if($ext!='.gif'&&$ext!='.jpg'){ 
            return array('file_name'=>'','save_path'=>'','error'=>3); 
        } 
        $filename=time().$ext; 
     } 
     if(0!==strrpos($save_dir,'/')){ 
        $save_dir.='/'; 
     } 
     //创建保存目录 
     if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){ 
        return array('file_name'=>'','save_path'=>'','error'=>5); 
     } 
     //获取远程文件所采用的方法  
     if($type){ 
        $ch=curl_init(); 
        $timeout=5; 
        curl_setopt($ch,CURLOPT_URL,$url); 
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout); 
        $img=curl_exec($ch); 
        curl_close($ch); 
     }else{ 
        ob_start();  
        readfile($url); 
        $img=ob_get_contents();  
        ob_end_clean();  
     } 
    //$size=strlen($img); 
    //文件大小  
     $fp2=@fopen($save_dir.$filename,'a'); 
     fwrite($fp2,$img); 
     fclose($fp2); 
     unset($img,$url); 
     return array('file_name'=>$filename,'save_path'=>$save_dir.$filename,'error'=>0); 
    } 

     private function xmltoarray($xml) {
        $arr = $this->xml_to_array ( $xml );
        $key = array_keys ( $arr );
         return $arr [$key [0]];
    }

    private  function xml_to_array($xml) {
        $reg = "/<(\\w+)[^>]*?>([\\x00-\\xFF]*?)<\\/\\1>/";
        if (preg_match_all ( $reg, $xml, $matches )) {
          $count = count ( $matches [0] );
          $arr = array ();
          for($i = 0; $i < $count; $i ++) {
            $key = $matches [1] [$i];
            $val = $this->xml_to_array ( $matches [2] [$i] ); // 递归
            if (array_key_exists ( $key, $arr )) {
              if (is_array ( $arr [$key] )) {
                if (! array_key_exists ( 0, $arr [$key] )) {
                  $arr [$key] = array (
                      $arr [$key] 
                  );
                }
              } else {
                $arr [$key] = array (
                    $arr [$key] 
                );
              }
              $arr [$key] [] = $val;
            } else {
              $arr [$key] = $val;
            }
          }
          return $arr;
        } else {
          return $xml;
        }
      }

      //分类
    public function gettype($booktype){
           $tytes = array(
              '生活时尚' => 11,
              '健康养生' => 11,
              '散文随笔' => 13,
              '总裁先生' => 16,
              '浪漫古言' => 13,
              '无限幻想' => 4,
              '玄幻奇幻' => 5,
              '官场职场' => 17,
              '历史军事' => 2,
              '社科教育' => 17,
              '武侠仙侠' => 7,
              '经管励志' => 17,
              '悬疑推理' => 1,
              '青春熟语' => 13,
              '纪实传记' => 2,
              '都市娱乐' => 11,
              '灵异惊悚' => 1,               
          );
          return $tytes[$booktype];
          // '1' => "悬疑",
          //   '2' => "历史",
          //   '3' => "军事",
          //   '4' => "玄幻",
          //   '5' => "奇幻",
          //   '6' => "仙侠",
          //   '7' => "武侠",
          //   '8' => "科幻",
          //   '9' => "游戏",
          //   '10' => "同人",
          //   '11' => "都市",
          //   '12' => "校园",
          //   '13' => "言情",
          //   '14' => "穿越",
          //   '15' => "重生",
          //   '16' => "豪门",
          //   '17' => "职场",
      }


}
