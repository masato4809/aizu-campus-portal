import * as React from 'react';
import { Box, styled } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { IFormEdit } from '@/script/Pages/Project/Edit/FormEdit';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { Icon } from '@/script/Component/Misc/Icon';
import { E_ICON } from '@/script/Enum/EIcon';
import {
  AppTrnProjectNotificationList,
  IAppTrnProjectNotification,
} from '@/script/Models/App/Trn/TrnProjectNotification';
import { InputField } from '@/script/Component/Form/InputField';
import { useIndexContext } from '@/script/Pages/Project/Edit/Index';
import { E_NOTIFICATION_TYPE } from '@/script/Enum/Server/App/ENotificationType';
import { responsiveSpacing } from '@/script/System/Responsive';

/**
 * 円形ボタン.
 */
const StyledCircleButton = styled(ButtonGeneral)(({ theme }) => ({
  minWidth: '48px',
  width: '48px',
  minHeight: '48px',
  height: '48px',
  borderRadius: '24px',
  [theme.breakpoints.down('md')]: {
    minWidth: '24px',
    width: '24px',
    minHeight: '24px',
    height: '24px',
    borderRadius: '12px',
  },
}));

interface IProps extends IPropsBase {
  formEdit: IFormEdit;
  onChange: (notificationList: IAppTrnProjectNotification[]) => void;
}
export const SlackNotification: React.FC<IProps> = ({
  sx,
  formEdit,
  onChange,
}) => {
  const { trnProjectList } = useIndexContext();
  const trnProject = trnProjectList.first();

  /**
   * フィールド追加が実行された.
   */
  const handleAdd = () => {
    const newList = formEdit.notificationList;
    const newId =
      newList.length === 0 ? 1 : Math.max(...newList.map(v => v.id)) + 1;
    newList.push({
      ...AppTrnProjectNotificationList.defaultInterface(),
      id: newId,
      trnProjectId: trnProject.id,
      notificationType: E_NOTIFICATION_TYPE.SLACK_CHANNEL,
    });
    onChange(newList);
  };

  /**
   * フィールド削除が実行された
   */
  const handleRemove = (id: number) => {
    const newList = formEdit.notificationList.filter(v => v.id !== id);
    onChange(newList);
  };

  /**
   * 入力フィールドが更新された.
   */
  const handleChangeValue = (id: number, newValue: string) => {
    const newList = formEdit.notificationList;
    const target = newList.find(v => v.id === id);
    if (!target) {
      return;
    }

    target.notificationValue = newValue;
    onChange(newList);
  };

  /**
   * リストに一件も存在しないとき.
   */
  if (!formEdit.notificationList.length) {
    return (
      <Box sx={sx}>
        <Box
          sx={{
            marginBottom: responsiveSpacing(2),
            display: 'flex',
            alignItems: 'center',
            gap: responsiveSpacing(2),
          }}
        >
          <TypoText>新規登録</TypoText>
          <StyledCircleButton onClick={handleAdd}>
            <Icon icon={E_ICON.ADD} />
          </StyledCircleButton>
        </Box>
      </Box>
    );
  }

  return (
    <Box
      sx={{
        display: 'flex',
        flexDirection: 'column',
        gap: responsiveSpacing(2),
        ...sx,
      }}
    >
      {formEdit.notificationList.map(notification => {
        return (
          <Box
            key={notification.id}
            sx={{
              display: 'flex',
              alignItems: 'center',
              gap: responsiveSpacing(2),
            }}
          >
            <InputField
              id="form-slack-notification-value"
              inputValue={notification.notificationValue}
              onChange={notificationValue =>
                handleChangeValue(notification.id, notificationValue)
              }
            />
            <StyledCircleButton onClick={handleAdd}>
              <Icon icon={E_ICON.ADD} />
            </StyledCircleButton>
            <StyledCircleButton onClick={() => handleRemove(notification.id)}>
              <Icon icon={E_ICON.REMOVE} />
            </StyledCircleButton>
          </Box>
        );
      })}
    </Box>
  );
};
