package tModel
type User struct{
	UserID int64 `gorm:"type:int;column:userID" json:"userID" `
	SecretKey string `gorm:"type:varchar;column:secretKey" json:"secretKey"`
	IsForbidden int `gorm:"type:int;column:isForbidden" json:"isForbidden"`
	UserName string `gorm:"type:string;column:userName" json:"userName"`
	UserRealName string `gorm:"type:string;column:userRealName" json:"userRealName"`
	IsSuper int `gorm:"type:int;column:isSuper" json:"isSuper"`
	BoundChannelID string `gorm:"type:string;column:boundChannelID" json:"boundChannelID"`
}

func (User) TableName() string{
	return "Tuser"
}