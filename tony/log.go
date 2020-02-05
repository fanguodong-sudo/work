package tonylog

import (
	"fmt"
	"github.com/wxnacy/wgo/arrays"
	"os"
	"runtime"
	"syscall"
	"time"
)

type tonyLog struct {
	data string
	dataType string
	date string
	url string
}

const (
	//日志地址
	FILE_PATH string = ""
	//报错级别
	LEVEL int = 0
)

var log []tonyLog

func Write(action string, str string) error {

	actionType := []string{
		"debug","info","warn","error","fatal","panic",
	}
	index := arrays.ContainsString(actionType, action)

	if index == -1{
		return fmt.Errorf("级别设置有误")
	}
	if index < LEVEL {
		return nil
	}

	infoLog := new(tonyLog)
	infoLog.data = str;
	infoLog.dataType = action
	infoLog.date = time.Now().Format("2006-01-02 15:04:05")
	infoLog.url = runFuncName(3)
	handle(*infoLog)
	return nil
}

//处理日志
func handle(singLog tonyLog){
	log = append(log, singLog)
	handleDone(log)
}

//写日志
func handleDone(log []tonyLog){
	var path string
	if len(FILE_PATH) > 0 {
		path = FILE_PATH;
	}else{
		path,_ = os.Getwd()
	}

	//使用文件锁
	syscall.ForkLock.Lock()

	fullPath,file := getFilePath(path)
	fp,err:= os.OpenFile(checkFilePath(fullPath,file),os.O_APPEND, os.ModeAppend)

	if(err != nil){
		fmt.Println(err)
	}

	for _,v := range log{
		_,err := fp.WriteString("["+v.date+"]"+"["+v.dataType+"]"+"["+v.url+"]"+"["+v.data+"]\r\n")
		if err != nil {
			panic("写入日志失败")
		}
	}
	err = fp.Close()

	//释放文件锁
	syscall.ForkLock.Unlock()

	if err != nil {
		panic("写入流关闭失败")
	}

	//情况数组
	log = []tonyLog{}
}

//获取默认日志路径
func getFilePath(path string) (fullPath string,file string) {
	//设置日志保存记录
	return path+"\\runtime",time.Now().Format("2006-01-02")+".log"
}

//判断目标文件是否存在，不存在就创建
func checkFilePath(path string,file string) string {
	//获取路径文件相关信息
	_,err := os.Stat(path)
	//如果存在错误，说明目标文件不存在，要创建
	if err != nil {
		os.MkdirAll(path,0777)
	}

	lastPath := path+"\\"+file

	//创建文件
	_,err = os.Stat(lastPath)
	if err != nil {
		_,err = os.Create(path+"\\"+file)
		if err != nil{
			//如果文件创建错误，抛出异常
			panic("文件创建失败")
		}
	}
	return lastPath
}

// 获取正在运行的函数名
func runFuncName(i int)string{
	pc := make([]uintptr,1)
	runtime.Callers(i,pc)
	f := runtime.FuncForPC(pc[0])
	return f.Name()
}
