package my

import (
	"io"
	"log"
	"os"
	"time"
)

type Tlog struct {
	Trace   *log.Logger
	Info    *log.Logger
	Warning *log.Logger
	Error   *log.Logger
}

func (this *Tlog) Init() *Tlog{
	path,_ := os.Getwd()
	file,err:= os.OpenFile(this.getFilePath(path),os.O_CREATE|os.O_WRONLY|os.O_APPEND,0666)
	if err != nil {
		log.Fatalln("Failed to open error log file:", err)
	}

	this.Trace   = log.New(io.MultiWriter(file, os.Stderr),"TRACE: ",log.Ldate|log.Ltime|log.Lshortfile)
	this.Info    = log.New(io.MultiWriter(file, os.Stderr),"INFO: ",log.Ldate|log.Ltime|log.Lshortfile)
	this.Warning = log.New(io.MultiWriter(file, os.Stderr),"WARNING: ",log.Ldate|log.Ltime|log.Lshortfile)
	this.Error   = log.New(io.MultiWriter(file, os.Stderr),"ERROR: ",log.Ldate|log.Ltime|log.Lshortfile)

	return this
}
//获取默认日志路径
func (this *Tlog) getFilePath(path string) (fullPath string) {
	//设置日志保存记录
	return path+"\\runtime"+"\\"+time.Now().Format("2006-01-02")+".log"
}
