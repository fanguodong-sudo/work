package my

import "fmt"

type Tpanic struct {

}

func(this *Tpanic) Index(){
	defer func() {
		err := recover()
		fmt.Println(err)
		fmt.Println("发送给管理员信息！")
	}()
	panic("111111111111")
}