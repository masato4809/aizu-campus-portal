import * as React from 'react';
import {
  Box,
  Checkbox,
  FormControl,
  FormControlLabel,
  Tooltip,
} from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { IFormEditValidationResult } from '@/script/Pages/Project/Edit/FormEditValidation';
import { LabelValue } from '@/script/Pages/Common/LabelValue';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { InputField } from '@/script/Component/Form/InputField';
import { InputFieldMultiline } from '@/script/Component/Form/InputFieldMultiline';
import { Avatar } from '@/script/Pages/Common/Avatar';
import { useIndexContext } from '@/script/Pages/Project/Edit/Index';
import { Icon } from '@/script/Component/Misc/Icon';
import { E_ICON } from '@/script/Enum/EIcon';
import { Closable } from '@/script/Component/Misc/Closable';
import { SlackNotification } from '@/script/Pages/Project/Edit/SlackNotification';
import { IAppTrnProjectNotification } from '@/script/Models/App/Trn/TrnProjectNotification';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';

export interface IFormEdit {
  id: number;
  name: string;
  explain: string;
  editNotification: boolean;
  notificationList: IAppTrnProjectNotification[];
}

interface IProps extends IPropsBase {
  formEdit: IFormEdit;
  validation: IFormEditValidationResult;
  handleUpdate: (newValues: Partial<IFormEdit>) => void;
}
export const FormEdit: React.FC<IProps> = ({
  sx,
  formEdit,
  validation,
  handleUpdate,
}) => {
  const { trnProjectList } = useIndexContext();
  const trnProject = trnProjectList.first();
  return (
    <FormControl
      sx={{
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'start',
        gap: responsiveSpacing(4),
        ...sx,
      }}
    >
      <LabelValue label="id" value={String(formEdit.id)} />
      <Box
        sx={{
          display: 'flex',
          alignItems: 'center',
        }}
      >
        <TypoText sx={{ minWidth: responsiveSize(200) }}>名前</TypoText>
        <InputField
          id="form-project-name"
          inputValue={formEdit.name}
          onChange={name => handleUpdate({ name })}
          visibleError
          errors={validation.data.name?.errors}
        />
      </Box>
      <Box
        sx={{
          width: '100%',
          display: 'flex',
          alignItems: 'center',
        }}
      >
        <TypoText sx={{ minWidth: responsiveSize(200) }}>説明</TypoText>
        <InputFieldMultiline
          sx={{
            width: '100%',
          }}
          id="form-project-explain"
          inputValue={formEdit.explain}
          fieldSx={{
            width: '100%',
          }}
          inputProps={{
            sx: {
              width: '100%',
              resize: 'vertical',
            },
          }}
          onChange={explain => handleUpdate({ explain })}
          visibleError
          errors={validation.data.explain?.errors}
          rows={5}
        />
      </Box>
      <Box sx={{ display: 'flex' }}>
        <Box>
          <TypoText
            sx={{ minWidth: responsiveSize(200), width: responsiveSize(200) }}
          >
            メンバー
          </TypoText>
        </Box>
        <Box
          sx={{
            marginTop: responsiveSpacing(2),
            display: 'flex',
            flexWrap: 'wrap',
            gap: responsiveSpacing(2),
          }}
        >
          {trnProject.trnProjectUser?.map(trnProjectUser => {
            return (
              <Avatar
                key={trnProjectUser.id}
                trnUser={trnProjectUser.trnUser}
              />
            );
          })}
        </Box>
      </Box>
      <Box
        sx={{
          display: 'flex',
        }}
      >
        <TypoText
          sx={{
            marginTop: responsiveSpacing(2),
            minWidth: responsiveSize(200),
            width: responsiveSize(200),
          }}
        >
          Slack通知
        </TypoText>
        <Box>
          <Box sx={{ display: 'flex' }}>
            <FormControlLabel
              control={
                <Checkbox
                  checked={formEdit.editNotification}
                  onChange={v =>
                    handleUpdate({ editNotification: v.target.checked })
                  }
                />
              }
              label={<TypoText>Slack通知を登録/編集する</TypoText>}
            />
            <Tooltip
              placement="top"
              title={
                <Box>
                  <TypoText>勤怠時に特定チャンネルに通知</TypoText>
                  <TypoText>SlackのチャンネルIDを入力</TypoText>
                </Box>
              }
            >
              <Box
                component="span"
                sx={{
                  display: 'flex',
                  alignItems: 'center',
                }}
              >
                <Icon icon={E_ICON.STAR} />
              </Box>
            </Tooltip>
          </Box>
          <Closable open={formEdit.editNotification}>
            <SlackNotification
              formEdit={formEdit}
              onChange={notificationList => handleUpdate({ notificationList })}
            />
          </Closable>
        </Box>
      </Box>
    </FormControl>
  );
};
