package my

import (
	"../tModel"
	"encoding/json"
	"fmt"
	"github.com/jinzhu/gorm"
	"github.com/mlogclub/simple"
)

type Tmysql struct {
	db *gorm.DB
}

func NewTmysql() *Tmysql{
	t := new(Tmysql)
	t.init()
	return t
}

func (this *Tmysql) Index() {
	this.Update()
	//this.Create()
}

//查询
func (this *Tmysql) Find() string{
	ret := &tModel.User{}
	if err := this.db.First(ret, "userID=? and isForbidden=?", 17, 0).Error; err != nil {
		panic(err)
	}

	fmt.Println(ret.UserID)
	fmt.Println(ret.SecretKey)
	fmt.Println(ret.UserName)
	fmt.Println(ret.BoundChannelID)
	fmt.Println(ret.UserRealName)

	b,_ := json.Marshal(ret)
	return string(b)
}
//删除记录
func (this *Tmysql) Delete(){
	dUser := &tModel.User{
		UserID:   26,
		//SecretKey:"x0jOKNv6xcUWzDxb57Eu",
	}
	if err := this.db.Where(dUser).Delete(tModel.User{}).Error;err != nil{
		panic(err)
	}
	fmt.Println("删除成功！")
}

//更新
func (this *Tmysql) Update(){
	if err := this.db.Model(&tModel.User{}).Where(&tModel.User{UserID:26}).Update("SecretKey","4535435234").Error;err != nil{
		panic(err)
	}
	fmt.Println("更新成功！")
}

//创建记录
func (this *Tmysql) Create(){
	newUser := tModel.User{
		UserID:   26,
		SecretKey:      "111111",
		IsForbidden:    0,
		UserName:       "11111",
		UserRealName:   "22222222",
		IsSuper:        0,
		BoundChannelID: "15",
	}
	if err := this.db.Create(newUser).Error; err != nil{
		panic(err)
	}
	fmt.Println("添加成功")
}

//配置mysql
func (this *Tmysql) init(){
	err := simple.OpenMySql("daojia:daojia580.com@tcp(192.168.0.124:63306)/dj_messager?charset=utf8&parseTime=True&loc=Local",
		5, 20, true) // 连接数据库
	if err != nil {
		fmt.Println("连接数据库失败")
	}
	this.db = simple.DB()
}
