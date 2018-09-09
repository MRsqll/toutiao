const request = require ('request');//发送请求
const cheerio = require ('cheerio');//编程语言
const async = require('async');//多次请求，请求一次完成后再请求下一次
const iconv = require('iconv-lite');//编码方式
const mysql = require('mysql');//连接数据库
const filter = require('bloom-filter-x');//哈希函数
const fs = require('fs');//连接数据库

//     let connection = mysql.createConnection({
//         host:'localhost',
//         user:'root',
//         password:'',
//         database:'toutiao'
//     })
//     let urls = [];
//     let obj = [];
// //查，去重
//     let select_mysql = 'select * from news';
//     connection.query(select_mysql,(err,results,fields)=>{
//         if(err) throw err;
//         results.forEach((v)=>{
//                 let url = v.url;
//                 if(filter.add(url)){
//                     urls.push(url);
//                 }
//             })
//         });
// function fetch_news(){
//     request({
//         url:'http://news.zol.com.cn/',
//         encoding:null
//     },(err,res,body)=>{
//         body = iconv.decode(body,'gb2312');
//         let $ = cheerio.load(body);
//         $('.content-list li').each((k,v)=>{
//             let t = $(v).find('.info-head a');
//             let title  = t.text();
//             let dsc =  $(v).find('p').contents().eq(0).text();
//             let url =t.attr('href');
//             let image_urls =$(v).find('img').attr('.src');
//             if(filter.add(url)){
//                 urls.push(url);//加入哈希函数的数组
//                 obj.push({
//                     'title':title,
//                     'dsc':dsc,
//                     'url':url,
//                     'image_urls':image_urls
//                 });
//             }
//         });
//         console.log(obj);
//         if(!urls.length){
//             let d = new Date();
//             console.log(d.toUTCString() + '抓取了一次，本次没有更新..')
//         }else {
//             let d = new Date();
//             console.log(d.toUTCString() + '抓取了一次，本次更新了'+ urls.length +'条');
//             async.eachLimit(obj,1,function (v,next) {
//                 request({
//                     url:v.url,
//                     encoding:null
//                 },(err,res,body)=>{
//                     if(err){
//                         console.log(err.message);
//                     }else{
//                         body = iconv.decode(body,'gb2312');
//                         let $ = cheerio.load(body);
//                         let pubtime = $('#pubtime_baidu').attr('content');
//                         let content = $('#article-content').html();
//                         let title = v.title;
//                         let dsc = v.dsc;
//                         let url = v.url;
//                         let image_url = v.image_urls;
//                         let cid = 1;
//                         let insert_mysql = 'insert into news (cid,title,dsc,image_urls,url,pubtime,content) values (?,?,?,?,?,?,?)';
//                         connection.query(insert_mysql,[cid,title,dsc,image_url,url,pubtime,content],(err,results,fields)=>{
//                             if (err) throw  err;
//                             console.log('OK');
//                         })
//                     }
//                     next(null);
//                 })
//             })
//         }
//     });
// }
// fetch_news();

// setInterval(fetch_news,3600000);


const con = mysql.createPool({
  host: 'localhost',
  port: 3306,
  user: 'root',
  password: '',
  database: 'toutiao'});

function feedFilter() {
  console.log('开始喂布隆过滤器');
  con.query('select * from news', (err, results) => {
    results.forEach(v => filter.add(v.url));
    console.log('喂完了');
    fetch();
  })
}

// 请求 news.zol.com.cn 找到你要的东西  放进数据库
function fetch() {
  console.log('开始抓取数据');
  let data = [];
  request(
    // 第一个参数
    {
      url: 'http://news.zol.com.cn',
      encoding: null,
    },
    // 第二个参数
    (err, res, body) => {
      console.log('总页面拿到了');
      body = iconv.decode(body, 'gb2312');
      let $ = cheerio.load(body);
      console.log('取出页面中的所有新闻开始过滤');
      $('.content-list li').each((k, v) => {
      	let t = $(v).find('.info-head a');
        let title  = t.text();
        let dsc =  $(v).find('p').contents().eq(0).text();
        let url =t.attr('href');
        let image_urls =$(v).find('img').attr('.src');
        let pubtime = $('#pubtime_baidu').attr('content');
        let content = $('#article-content').html();
        if (filter.add(url)) {
          data.push({
            cid: 1,
            title: title,
            dsc: dsc,
            image: image_urls,
            url: url,
            pubtime: pubtime,
            content: content
          })
        }
      });
      console.log(`过滤完成,还剩${data.length}条数据,开始插入数据库`);
      async.eachLimit(
        data, 1, (v, next) => {
          con.query(
            'INSERT INTO `news` ( `cid`, `title`, `dsc`, `image_urls`, `url`, `pubtime`, `content`) VALUES (?,?,?,?,?,?,?)',
            [v.cid, v.title, v.dsc, v.image, v.url, v.pubtime, v.content],
            (err, result) => {
              
              next(null);
            }
          )
        },
        () => {
          console.log('全部插入完成');
          setTimeout(fetch, 60 * 1000)
        }
      )
    }
  )
}

feedFilter();